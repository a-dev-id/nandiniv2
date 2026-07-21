<?php

namespace App\Services\Voucher;

use App\Models\IssuedVoucher;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class VoucherPdfService
{
    public function render(IssuedVoucher $voucher): string
    {
        $voucher->loadMissing('voucher');
        $imagePath = $voucher->voucher?->image;
        $imageUrl = $imagePath ? Storage::disk('public')->url($imagePath) : null;

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml(view('pdf.voucher', compact('voucher', 'imageUrl'))->render());
        $dompdf->render();

        return $dompdf->output();
    }

    public function filename(IssuedVoucher $voucher): string
    {
        return 'nandini-voucher-' . strtolower($voucher->voucher_code) . '.pdf';
    }
}
