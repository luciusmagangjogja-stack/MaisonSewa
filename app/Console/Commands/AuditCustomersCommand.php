<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AuditCustomersCommand extends Command
{
    protected $signature = 'customers:audit';

    protected $description = 'Audit data customer (read-only): duplicates after phone normalization, invalid/empty phone, customer without rentals, rentals without customer.';

    public function handle(): int
    {
        $customers = Customer::query()->withTrashed(false)->get(['id', 'name', 'phone']);
        $totalCustomers = $customers->count();

        $rows = $customers->map(function (Customer $c) {
            $raw = $c->phone;
            return [
                'id' => $c->id,
                'name' => $c->name,
                'raw_phone' => $raw,
                'norm_phone' => $this->normalizePhone($raw), // canonical: 628xxxxxxxxxx
                'name_key' => Str::lower(trim((string) $c->name)), // for case-insensitive match
            ];
        });

        $this->outputHeader('Customer Audit (read-only) - SewaJas');
        $this->info('Total customers: ' . $totalCustomers);

        // 1) Empty phone
        $empty = $rows->filter(fn($r) => $this->isEmptyPhone($r['raw_phone']))->values();
        $this->outputCategoryOkOrWarn(
            title: '1) Nomor Handphone kosong atau null',
            count: $empty->count(),
            items: $empty,
            okWhenZero: true,
            itemFormatter: function ($it) {
                return [
                    'ID' => $it['id'],
                    'Nama' => (string) $it['name'],
                    'HP Asli' => $this->stringifyPhone($it['raw_phone']),
                    'HP Normalisasi' => $it['norm_phone'],
                    'Rekomendasi' => 'Isi nomor HP yang valid (canonical: 628xxxxxxxxxx).',
                ];
            },
            recommendation: 'Periksa customer yang belum mengisi nomor HP.'
        );

        // 2) Invalid phone format (after normalization)
        $invalid = $rows->filter(function ($r) {
            if ($this->isEmptyPhone($r['raw_phone'])) return false;
            if ($r['norm_phone'] === '') return true;
            return !$this->isValidCanonicalPhone($r['norm_phone']);
        })->values();
        $this->outputCategoryOkOrWarn(
            title: '2) Nomor Handphone format tidak valid (setelah normalisasi)',
            count: $invalid->count(),
            items: $invalid,
            okWhenZero: true,
            itemFormatter: function ($it) {
                return [
                    'ID' => $it['id'],
                    'Nama' => (string) $it['name'],
                    'HP Asli' => $this->stringifyPhone($it['raw_phone']),
                    'HP Normalisasi' => $it['norm_phone'],
                    'Rekomendasi' => 'Pastikan canonical: 628xxxxxxxxxx (angka saja).',
                ];
            },
            recommendation: 'Gunakan format 0812xxxx / 62812xxxx / +62812xxxx; sistem canonical-nya harus menjadi 628xxxxxxxxxx.'
        );

        // 3) Duplicate Nomor HP (after normalization)
        $dupePhoneGroups = $rows
            ->filter(fn($r) => $r['norm_phone'] !== '' && $this->isValidCanonicalPhone($r['norm_phone']))
            ->groupBy('norm_phone')
            ->filter(fn(Collection $g) => $g->count() > 1);

        $this->outputDuplicatesByPhone(
            title: '3) Duplicate Nomor Handphone (setelah normalisasi)',
            groups: $dupePhoneGroups,
            recommendation: 'Jika memang duplikat orang yang sama, merge. Jika berbeda orang, klarifikasi nomor HP mana yang benar.'
        );

        // 4) Duplicate Nama + Nomor HP (case-insensitive)
        $dupeNamePhoneGroups = $rows
            ->filter(fn($r) => $r['norm_phone'] !== '' && $this->isValidCanonicalPhone($r['norm_phone']))
            ->groupBy(fn($r) => $r['name_key'] . '|' . $r['norm_phone'])
            ->filter(fn(Collection $g) => $g->count() > 1);

        $this->outputDuplicatesByNamePhone(
            title: '4) Duplicate Nama + Nomor Handphone (case-insensitive)',
            groups: $dupeNamePhoneGroups,
            recommendation: 'Periksa apakah ini customer yang sama (merge) atau ada penulisan nama/nomor ganda.'
        );

        // 5) Customer without Rental
        $customerIdsWithRental = Customer::query()
            ->whereIn('id', function ($q) {
                $q->select('customer_id')->from('rentals')->distinct();
            })
            ->pluck('id')
            ->all();

        $customersWithoutRental = $customers->filter(fn(Customer $c) => !in_array($c->id, $customerIdsWithRental, true));

        $customersWithoutRentalRows = $customersWithoutRental->map(function (Customer $c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'raw_phone' => $c->phone,
                'norm_phone' => $this->normalizePhone($c->phone),
            ];
        });

        $this->outputCategoryOkOrWarn(
            title: '5) Customer tanpa Rental',
            count: $customersWithoutRentalRows->count(),
            items: $customersWithoutRentalRows,
            okWhenZero: true,
            itemFormatter: function ($it) {
                return [
                    'ID' => $it['id'],
                    'Nama' => (string) $it['name'],
                    'HP Asli' => $this->stringifyPhone($it['raw_phone']),
                    'HP Normalisasi' => $it['norm_phone'],
                    'Rekomendasi' => 'Verifikasi apakah customer ini memang pernah rental atau seharusnya diarsip.'
                ];
            },
            recommendation: 'Tinjau historis customer vs rental.'
        );

        // 6) Rental without Customer (foreign key rusak / orphan)
        // Audit rental rows where customer_id is null or not present in customers table.
        $validCustomerIds = $customers->pluck('id')->all();
        $rentalRows = \DB::table('rentals')->select('id', 'customer_id')->get();

        $rentalsWithoutCustomer = $rentalRows->filter(function ($r) use ($validCustomerIds) {
            $cid = $r->customer_id;
            if (is_null($cid)) return true;
            return !in_array($cid, $validCustomerIds, true);
        })->values();

        $rentalAuditRows = $rentalsWithoutCustomer->map(function ($r) {
            return [
                'rental_id' => $r->id,
                'customer_id' => $r->customer_id,
            ];
        });

        $this->outputCategoryOkOrWarn(
            title: '6) Rental tanpa Customer (customer_id null/invalid/orphan)',
            count: $rentalAuditRows->count(),
            items: $rentalAuditRows,
            okWhenZero: true,
            itemFormatter: function ($it) {
                return [
                    'Rental ID' => $it['rental_id'],
                    'customer_id' => $it['customer_id'] === null ? 'null' : (string) $it['customer_id'],
                    'Rekomendasi' => 'Pastikan customer_id pada rental valid dan customer belum dihapus tanpa semestinya.'
                ];
            },
            recommendation: 'Periksa integritas referensi rentals -> customers.'
        );

        // 7) Verify search identity: 0812xxxx, 62812xxxx, +62812xxxx
        $equivalence = $this->verifySearchEquivalence();
        $this->outputEquivalence(
            title: '7) Verifikasi identik untuk input search: 0812xxxx / 62812xxxx / +62812xxxx',
            checked: $equivalence['checked'],
            mismatch: $equivalence['mismatch'],
            recommendation: $equivalence['mismatch'] === 0
                ? 'OK: normalisasi sudah konsisten untuk pola 08/62/+62.'
                : 'Terjadi mismatch pada equivalence normalisasi untuk sebagian data.'
        );

        $this->info('Audit selesai. Tidak ada data yang diubah.');
        return Command::SUCCESS;
    }

    /**
     * Canonical normalization:
     * - hapus spasi
     * - hapus tanda "-"
     * - hapus "()"
     * - hapus karakter selain angka dan "+"
     * - ubah +62 menjadi 62
     * - ubah 08 menjadi 628...
     * - hasil akhir: 628xxxxxxxxxx (canonical)
     */
    private function normalizePhone($phone): string
    {
        if ($phone === null) return '';

        $s = trim((string) $phone);
        if ($s === '') return '';

        // remove spaces and some separators
        $s = str_replace([' '], '', $s);
        $s = str_replace(['-'], '', $s);
        $s = str_replace(['('], '', $s);
        $s = str_replace([')'], '', $s);

        // keep digits and '+'
        $s = preg_replace('/[^0-9\+]/', '', $s);
        if ($s === '' || $s === '+') return '';

        // convert +62 -> 62 (canonical uses 62 prefix)
        if (str_starts_with($s, '+62')) {
            $digits = '62' . substr($s, 3);
        } elseif (str_starts_with($s, '62')) {
            $digits = $s;
        } elseif (str_starts_with($s, '08')) {
            // 08xxxxxxxx -> 628xxxxxxxxxx
            $digits = '62' . substr($s, 1); // replaces leading 0 with 62
        } elseif (str_starts_with($s, '0')) {
            // generic: 0XXXXXXXX -> 62XXXXXXXX
            $digits = '62' . substr($s, 1);
        } else {
            // fallback: treat as missing country code
            $digits = '62' . $s;
        }

        // final: must be digits only
        $digits = preg_replace('/\D+/', '', $digits);

        return $digits;
    }

    private function isEmptyPhone($raw): bool
    {
        return $raw === null || trim((string) $raw) === '';
    }

    private function isValidCanonicalPhone(string $canonical): bool
    {
        // canonical must start with 62 and length 10-13 (62 + 8-11 digits)
        if ($canonical === '') return false;
        if (!preg_match('/^\d+$/', $canonical)) return false;
        if (!Str::startsWith($canonical, '62')) return false;
        $len = strlen($canonical);
        return $len >= 10 && $len <= 13;
    }

    private function stringifyPhone($v): string
    {
        if ($v === null) return 'null';
        $s = (string) $v;
        $s = trim($s);
        return $s === '' ? '""' : $s;
    }

    private function outputHeader(string $title): void
    {
        $this->newLine();
        $this->line(str_repeat('=', 70));
        $this->info($title);
        $this->line(str_repeat('=', 70));
    }

    private function outputCategoryOkOrWarn(
        string $title,
        int $count,
        $items,
        bool $okWhenZero,
        callable $itemFormatter,
        string $recommendation
    ): void {
        $this->line('');

        if ($okWhenZero && $count === 0) {
            $this->outputOk($title . ' : 0 (OK)');
            return;
        }

        if ($count === 0) {
            $this->warn($title . ' : 0');
            $this->line('Rekomendasi tindakan: ' . $recommendation);
            return;
        }

        $this->warn($title . ' : ' . $count);

        $collection = $items instanceof Collection ? $items : collect($items);
        $max = 80;
        $slice = $collection->take($max);

        if ($slice->count() > 0) {
            $table = [];
            foreach ($slice as $it) {
                $table[] = $itemFormatter($it);
            }

            $header = array_keys($table[0]);
            $rows = array_map(fn($r) => array_values($r), $table);

            $this->table($header, $rows);

            if ($collection->count() > $max) {
                $this->line('... dipotong menampilkan maksimal ' . $max . ' baris');
            }
        }

        $this->line('Rekomendasi tindakan: ' . $recommendation);
    }

    private function outputDuplicatesByPhone(string $title, Collection $groups, string $recommendation): void
    {
        $this->line('');

        if ($groups->isEmpty()) {
            $this->outputOk($title . ' : OK (0)');
            return;
        }

        $customerCount = $groups->flatten(1)->count();
        $this->warn($title . ' : ' . $groups->count() . ' group, ' . $customerCount . ' customers');

        $maxGroups = 20;
        $showGroups = $groups->take($maxGroups);

        $table = [];
        foreach ($showGroups as $norm => $g) {
            foreach ($g as $it) {
                $table[] = [
                    'Norm HP (canonical)' => (string) $norm,
                    'ID' => $it['id'],
                    'Nama' => $it['name'],
                    'HP Asli' => $this->stringifyPhone($it['raw_phone']),
                    'Rekomendasi' => $recommendation,
                ];
            }
        }

        $this->table(['Norm HP (canonical)', 'ID', 'Nama', 'HP Asli', 'Rekomendasi'], array_map(fn($r) => array_values($r), $table));

        if ($groups->count() > $maxGroups) {
            $this->line('... dipotong menampilkan maksimal ' . $maxGroups . ' group');
        }

        $this->line('Rekomendasi tindakan: ' . $recommendation);
    }

    private function outputDuplicatesByNamePhone(string $title, Collection $groups, string $recommendation): void
    {
        $this->line('');

        if ($groups->isEmpty()) {
            $this->outputOk($title . ' : OK (0)');
            return;
        }

        $customerCount = $groups->flatten(1)->count();
        $this->warn($title . ' : ' . $groups->count() . ' group, ' . $customerCount . ' customers');

        $maxGroups = 20;
        $showGroups = $groups->take($maxGroups);

        $table = [];
        foreach ($showGroups as $key => $g) {
            foreach ($g as $it) {
                $table[] = [
                    'Nama (case-insensitive)' => $it['name_key'],
                    'Norm HP (canonical)' => $it['norm_phone'],
                    'ID' => $it['id'],
                    'Nama Asli' => $it['name'],
                    'HP Asli' => $this->stringifyPhone($it['raw_phone']),
                    'Rekomendasi' => $recommendation,
                ];
            }
        }

        $this->table(['Nama (case-insensitive)', 'Norm HP (canonical)', 'ID', 'Nama Asli', 'HP Asli', 'Rekomendasi'], array_map(fn($r) => array_values($r), $table));

        if ($groups->count() > $maxGroups) {
            $this->line('... dipotong menampilkan maksimal ' . $maxGroups . ' group');
        }

        $this->line('Rekomendasi tindakan: ' . $recommendation);
    }

    private function outputEquivalence(string $title, int $checked, int $mismatch, string $recommendation): void
    {
        $this->line('');
        $this->line($title);

        if ($mismatch === 0) {
            $this->outputOk('   checked: ' . $checked . ' | mismatch: 0');
        } else {
            $this->error('   checked: ' . $checked . ' | mismatch: ' . $mismatch);
        }

        $this->line('   ' . $recommendation);
    }

    private function verifySearchEquivalence(): array
    {
        // We validate that given a canonical phone, these inputs map to the same canonical:
        // 0812xxxx  (leading 08) => 62 + rest
        // 62812xxxx (leading 62) => same
        // +62812xxxx (leading +62) => same
        $customers = Customer::query()->withTrashed(false)->get(['phone']);
        $checked = 0;
        $mismatch = 0;

        foreach ($customers as $c) {
            $canonical = $this->normalizePhone($c->phone);
            if ($canonical === '') continue;
            if (!$this->isValidCanonicalPhone($canonical)) continue;

            // canonical is 62 + 8-11 digits.
            // Create synthetic equivalents:
            // canonical: 62xxxxxxxxx
            // - 08xxxxxxxxx (replace leading 62 with 0, keep remaining)
            // - 62812... (already canonical)
            // - +62812...
            $after62 = substr($canonical, 2);
            if ($after62 === '') continue;

            $input08 = '0' . $after62;              // => 0xxxxxxxx
            $input62 = $canonical;                // => 62xxxxxxxx
            $inputPlus62 = '+' . $canonical;     // => +62xxxxxxxx

            $n1 = $this->normalizePhone($input08);
            $n2 = $this->normalizePhone($input62);
            $n3 = $this->normalizePhone($inputPlus62);

            $checked++;
            if (!($n1 === $n2 && $n2 === $n3)) {
                $mismatch++;
            }
        }

        return ['checked' => $checked, 'mismatch' => $mismatch];
    }

    private function outputOk(string $message): void
    {
        $this->line('<info>' . $message . '</info>');
    }
}

