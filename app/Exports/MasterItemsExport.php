<?php

namespace App\Exports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterItemsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = MasterItem::with('kategori')->get();

        return $data->map(function ($item, $index) {

            $hargaJual = $item->harga_beli + ($item->harga_beli * $item->laba / 100);

            return [
                'no' => $index + 1,
                'kategori' => $item->kategori->nama ?? '-',
                'nama_item' => $item->nama,
                'supplier' => $item->supplier,
                'harga_beli' => $item->harga_beli,
                'laba' => $item->laba,
                'harga_jual' => round($hargaJual),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Nama Item',
            'Supplier',
            'Harga Beli',
            'Laba (%)',
            'Harga Jual',
        ];
    }
}
