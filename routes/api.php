<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CrossAuthController;
use App\Http\Controllers\Api\DocumentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Approval API — v1
|--------------------------------------------------------------------------
| Base URL : /api/v1
| Auth     : Bearer token (obtain via POST /api/v1/auth/login)
|
| All protected routes require:
|   Authorization: Bearer <token>
|   Accept: application/json
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public: Authentication ───────────────────────────────────────────
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // ── Public: Cross-VSuite Chairman authentication ─────────────────────
    // A second VSuite instance posts the Chairman's email here.
    // If the email matches an active Chairman in THIS instance, a token is returned.
    Route::post('/cross-auth/chairman', [CrossAuthController::class, 'chairmanVerify']);

    // Issues a one-time 5-minute web-login token (verifies email + password).
    // Used by VMRFDU-VSuite to redirect the Chairman into this instance's UI.
    Route::post('/cross-auth/generate-login-token', [CrossAuthController::class, 'generateLoginToken']);

    // ── Protected routes ─────────────────────────────────────────────────
    Route::middleware('api.auth')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/auth/me',      [AuthApiController::class, 'me']);

        // Documents — listing
        Route::get('/documents',           [DocumentApiController::class, 'index']);
        Route::get('/documents/pending',   [DocumentApiController::class, 'pending']);
        Route::get('/documents/my',        [DocumentApiController::class, 'my']);
        Route::get('/documents/completed', [DocumentApiController::class, 'completed']);
        Route::get('/documents/{id}',      [DocumentApiController::class, 'show']);
        Route::get('/documents/{id}/approval-log', [DocumentApiController::class, 'approvalLog']);

        // Documents — actions
        Route::post('/documents/{id}/approve',          [DocumentApiController::class, 'approve']);
        Route::post('/documents/{id}/chairman-approve', [DocumentApiController::class, 'chairmanApprove']);
        Route::post('/documents/{id}/reject',   [DocumentApiController::class, 'reject']);
        Route::post('/documents/{id}/hold',     [DocumentApiController::class, 'hold']);
        Route::post('/documents/{id}/noted',    [DocumentApiController::class, 'noted']);
        Route::post('/documents/{id}/pending',  [DocumentApiController::class, 'markPending']);
        Route::post('/documents/{id}/discuss',  [DocumentApiController::class, 'discuss']);
        Route::post('/documents/{id}/forward',  [DocumentApiController::class, 'forward']);
        Route::post('/documents/{id}/comment',  [DocumentApiController::class, 'comment']);
        Route::post('/documents/{id}/complete', [DocumentApiController::class, 'complete']);
    });
});
