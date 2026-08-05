<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AuditQrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental:audit-qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit rental QR codes runtime (read-only): list QR paths, check file existence, extract embedded URL strings from SVG.';

    public function handle(): int
    {
        $this->line('== rental:audit-qr (read-only) ==');

        $rentals = Rental::query()->orderBy('id')->get(['id', 'invoice_number', 'qr_code']);

        $total = $rentals->count();
        $old = 0;
        $new = 0;
        $missing = 0;
        $invalid = 0;

        if ($total === 0) {
            $this->warn('No rental records found.');
            return self::SUCCESS;
        }

        $this->table(
            ['Rental ID', 'Invoice', 'QR File', 'QR Exists', 'QR URL (embedded string) / Status'],
            []
        );

        $rows = [];

        foreach ($rentals as $rental) {
            $id = $rental->id;
            $invoice = $rental->invoice_number;
            $qrPath = (string) ($rental->qr_code ?? '');

            $qrExists = 'NO';
            $embeddedUrl = '';
            $status = 'MISSING_QR_CODE_PATH';

            if (trim($qrPath) === '' || $qrPath === 'null') {
                $missing++;
                $status = 'MISSING_QR_CODE_PATH';
            } else {
                // qr_code in this project is a relative path like: qrcodes/rentals/{invoice}.svg
                $fullPath = storage_path('app/public/' . $qrPath);

                if (!is_string($fullPath) || trim($fullPath) === '' || !file_exists($fullPath)) {
                    $missing++;
                    $qrExists = 'NO';
                    $status = 'QR_FILE_NOT_FOUND';
                } else {
                    $qrExists = 'YES';

                    $svg = @file_get_contents($fullPath);
                    if ($svg === false) {
                        $invalid++;
                        $status = 'QR_SVG_READ_FAILED';
                    } else {
                        // Extract likely URL(s) embedded in SVG
                        // Simple heuristic: any substring starting with http(s):// or /rentals/
                        // Note: SVG text encoding varies; we keep raw URL substring(s) found.
                        $urls = [];

                        // Match full absolute URLs
                        if (preg_match_all('~https?://[^\"\s<]+~i', $svg, $m)) {
                            $urls = array_merge($urls, $m[0]);
                        }

                        // Match relative paths
                        if (preg_match_all('~/rentals/[^\"\s<]+~i', $svg, $m2)) {
                            $urls = array_merge($urls, $m2[0]);
                        }

                        $urls = array_values(array_unique($urls));

                        if (count($urls) === 0) {
                            $invalid++;
                            $status = 'QR_URL_NOT_FOUND_IN_SVG';
                        } else {
                            // Choose the first relevant rentals URL
                            $embeddedUrl = $urls[0];

                            $isOld = Str::contains($embeddedUrl, '/rentals/') && !Str::contains($embeddedUrl, '/rentals/scan/');
                            $isNew = Str::contains($embeddedUrl, '/rentals/scan/');

                            if ($isOld) {
                                $old++;
                                $status = 'OLD_FORMAT';
                            } elseif ($isNew) {
                                $new++;
                                $status = 'NEW_FORMAT';
                            } else {
                                $invalid++;
                                $status = 'UNKNOWN_FORMAT';
                            }
                        }
                    }
                }
            }

            $qrUrlDisplay = '';
            if ($qrExists === 'YES' && $embeddedUrl !== '') {
                $qrUrlDisplay = $embeddedUrl;
            }

            $rows[] = [
                $id,
                $invoice,
                $qrPath,
                $qrExists,
                $qrUrlDisplay . (trim($status) !== '' ? ' | ' . $status : ''),
            ];
        }

        $this->table(
            ['Rental ID', 'Invoice', 'QR File', 'QR Exists', 'QR URL (embedded string) / Status'],
            $rows
        );

        $this->line('== Summary ==');
        $this->line("Total QR: {$total}");
        $this->line("QR Old Format: {$old}");
        $this->line("QR New Format: {$new}");
        $this->line("QR Missing: {$missing}");
        $this->line("QR Invalid: {$invalid}");

        return self::SUCCESS;
    }
}

