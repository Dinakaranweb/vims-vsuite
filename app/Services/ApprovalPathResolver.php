<?php

namespace App\Services;

use App\Models\User;

/**
 * Computes the document approval chain from the creator's division,
 * payment involvement, amount, and purchase flag. Shared by the web
 * (DocumentApprovalController) and mobile (DocumentApiController) create
 * flows so routing rules never drift between the two.
 */
class ApprovalPathResolver
{
    /**
     * @return array{sequence: array<int, string>, current_approver: string}
     */
    public static function resolve(
        User $user,
        string $isPaymentInvolved,
        float $amount,
        string $isPurchase = 'N',
        ?string $initialTo = null,
    ): array {
        $isClinical = ($user->division ?? '') === 'Clinical';
        $initialTo  = $initialTo ?: ($isClinical ? 'Medical Director' : 'General Manager');

        $sequence = [];

        // Staff-created documents go to their own department HOD first;
        // HODs (and other roles) go straight into the Medical Director / GM chain.
        if (($user->role ?? '') === 'Staff' && !empty($user->department)) {
            $sequence[] = $user->department;
        }

        $sequence[] = $initialTo;

        if ($isClinical) {
            if ($initialTo !== 'Medical Director') $sequence[] = 'Medical Director';
            if ($initialTo !== 'General Manager')  $sequence[] = 'General Manager';
        } else {
            if ($initialTo !== 'General Manager')  $sequence[] = 'General Manager';
            if ($initialTo !== 'Medical Director')  $sequence[] = 'Medical Director';
        }

        $sequence = array_values(array_unique($sequence));

        if ($isPaymentInvolved === 'Y') {
            $sequence = array_merge($sequence, self::paymentTail($amount, $isPurchase));
        }

        return [
            'sequence'         => $sequence,
            'current_approver' => $sequence[0],
        ];
    }

    /**
     * The department names that make up a payment-routing tail (as opposed to the
     * Clinical/Non-Clinical Medical Director / General Manager chain that precedes it).
     * Used by DocumentApprovalController::handleEnterAmount() to find where a document's
     * existing tail starts, so it can be replaced once the real amount becomes known.
     */
    public const PAYMENT_TAIL_DEPARTMENTS = [
        'STB Office', 'Chairman', 'Purchase Head', 'Purchase Head Chennai',
        'PA to Chairman', 'Finance Head Salem',
    ];

    /**
     * The payment-routing steps for a given amount/purchase flag, split out from resolve()
     * so it can be recomputed later once a document's real amount becomes known (it's often
     * unset/0 at creation time - see handleEnterAmount()).
     *
     * @return array<int, string>
     */
    public static function paymentTail(float $amount, string $isPurchase = 'N'): array
    {
        if ($amount > 200000) {
            // High value (>2 Lakhs) — STB Office → Chairman → PA to Chairman (selects Finance Head)
            $tail = ['STB Office', 'Chairman'];
            if ($isPurchase === 'Y') {
                $tail[] = 'Purchase Head Chennai';
            }
            $tail[] = 'PA to Chairman';
        } else {
            // ≤2 Lakhs — go directly to Finance Head Salem (no PA step)
            $tail = [];
            if ($isPurchase === 'Y') {
                $tail[] = 'Purchase Head';
            }
            $tail[] = 'Finance Head Salem';
        }

        return $tail;
    }
}
