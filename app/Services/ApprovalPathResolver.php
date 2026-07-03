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

        $sequence = [$initialTo];

        if ($isClinical) {
            if ($initialTo !== 'Medical Director') $sequence[] = 'Medical Director';
            if ($initialTo !== 'General Manager')  $sequence[] = 'General Manager';
        } else {
            if ($initialTo !== 'General Manager')  $sequence[] = 'General Manager';
            if ($initialTo !== 'Medical Director')  $sequence[] = 'Medical Director';
        }

        $sequence = array_values(array_unique($sequence));

        if ($isPaymentInvolved === 'Y') {
            if ($amount > 200000) {
                // High value (>2 Lakhs) — STB Office → Chairman → PA to Chairman (selects Finance Head)
                $sequence[] = 'STB Office';
                $sequence[] = 'Chairman';
                if ($isPurchase === 'Y') {
                    $sequence[] = 'Purchase Head Chennai';
                }
                $sequence[] = 'PA to Chairman';
            } else {
                // ≤2 Lakhs — go directly to Finance Head Salem (no PA step)
                if ($isPurchase === 'Y') {
                    $sequence[] = 'Purchase Head';
                }
                $sequence[] = 'Finance Head Salem';
            }
        }

        return [
            'sequence'         => $sequence,
            'current_approver' => $sequence[0],
        ];
    }
}
