<?php

namespace App\Services\Affiliate\Reports;

use Symfony\Component\HttpFoundation\StreamedResponse;

class SafeCsvWriter
{
    public function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers, ',', '"', '');

            foreach ($rows as $row) {
                fputcsv($output, array_map([$this, 'escape'], array_values($row)), ',', '"', '');
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function escape(mixed $value): string
    {
        $value = (string) ($value ?? '');

        return preg_match('/^[=+\-@]/u', $value) ? "'".$value : $value;
    }
}
