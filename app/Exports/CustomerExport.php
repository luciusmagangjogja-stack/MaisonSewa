<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return Customer::query()
            ->select('id', 'name', 'phone', 'email', 'address', 'is_blacklisted', 'branch_id', 'created_at')
            ->with('branch:id,name')
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Nama',
            'No. HP',
            'Email',
            'Alamat',
            'Status',
            'Cabang',
            'Tanggal Daftar',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->phone,
            $customer->email,
            $customer->address,
            $customer->is_blacklisted ? 'Blacklist' : 'Aktif',
            optional($customer->branch)->name,
            $customer->created_at?->format('d M Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
                ],
            ],
        ];
    }
}
