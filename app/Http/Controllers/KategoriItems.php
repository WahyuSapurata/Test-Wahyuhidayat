<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KategoriItems extends Controller
{
    public function index()
    {
        return view('kategori_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;

        $data_search = KategoriItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');

        $data_search = $data_search->select('kode', 'nama')->orderBy('id')->get();


        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = KategoriItem::find($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        return view('kategori_items.form.index', $data);
    }

    public function singleView($kode)
    {
    $data['data'] = KategoriItem::with('masterItems')
        ->where('kode', $kode)
        ->firstOrFail();

    return view('kategori_items.single.index', $data);
}

    public function formSubmit(Request $request, $method, $id = 0)
    {
        if ($method == 'new') {
            $data_item = new KategoriItem;
            $kode = KategoriItem::count('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = KategoriItem::find($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->kode = $kode;
        $data_item->save();

        return redirect('kategori-items');
    }

    public function delete($id)
    {
         $item = KategoriItem::findOrFail($id);

        // Hapus data dari database
    $item->delete();

    return redirect('kategori-items');
    }

    public function updateRandomData()
    {
                $data = KategoriItem::get();
        foreach($data as $item)
        {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->kode = $kode;
            $item->save();
        }
    }

    public function printPdf($kode)
{
    $kategori = KategoriItem::with('masterItems')
        ->where('kode', $kode)
        ->firstOrFail();

    $pdf = Pdf::loadView('kategori_items.pdf', [
        'kategori' => $kategori,
        'tanggal' => Carbon::now()
    ]);

    $pdf->setPaper('A4', 'portrait');

    return $pdf->stream('kategori-item.pdf');
}
}
