<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\IssuedVoucher;
use Illuminate\View\View;

class VoucherVerificationController extends Controller
{
    public function __invoke(string $token): View
    {
        $issuedVoucher = IssuedVoucher::query()
            ->where('verification_token_hash', hash('sha256', $token))
            ->firstOrFail();

        return view('voucher.verify', ['issuedVoucher' => $issuedVoucher]);
    }
}
