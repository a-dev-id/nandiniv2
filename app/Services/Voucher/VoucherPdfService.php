<?php

namespace App\Services\Voucher;

use App\Models\IssuedVoucher;
use Dompdf\Dompdf;
use Dompdf\Options;

class VoucherPdfService
{
    public function render(IssuedVoucher $voucher): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml(view('pdf.voucher', compact('voucher'))->render());
        $dompdf->render();

        return $dompdf->output();
    }

    public function filename(IssuedVoucher $voucher): string
    {
        return 'nandini-voucher-' . strtolower($voucher->voucher_code) . '.pdf';
    }
}
