<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Exports\MasterItemsExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');
       if (!empty($hargamin)) {
    $data_search = $data_search->where('harga_beli', '>=', $hargamin);
}

if (!empty($hargamax)) {
    $data_search = $data_search->where('harga_beli', '<=', $hargamax);
}

        $data_search = $data_search->select('kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'foto')->orderBy('id')->get();


        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

public function formView($method, $id = 0)
{
    if ($method == 'new') {
        $item = new MasterItem();
    } else {
        $item = MasterItem::findOrFail($id);
    }

    $data['item'] = $item;
    $data['method'] = $method;
    $data['kategoriItems'] = KategoriItem::all();

    return view('master_items.form.index', $data);
}

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->first();
        return view('master_items.single.index', $data);
    }

   public function formSubmit(Request $request, $method, $id = 0)
{
    if ($method == 'new') {
        $data_item = new MasterItem;

        $kode = MasterItem::count('id') + 1;
        $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

        sleep(3);
    } else {
        $data_item = MasterItem::find($id);
        $kode = $data_item->kode;
    }

    $data_item->nama = $request->nama;
    $data_item->harga_beli = $request->harga_beli;
    $data_item->laba = $request->laba;
    $data_item->kode = $kode;
    $data_item->supplier = $request->supplier;
    $data_item->jenis = $request->jenis;

    $data_item->kategori_id = $request->kategori_id;

    // upload foto
    if ($request->hasFile('foto')) {

        if (!empty($data_item->foto)) {
            $oldPath = public_path('uploads/master_items/' . $data_item->foto);

            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $file = $request->file('foto');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/master_items'), $fileName);

        $data_item->foto = $fileName;
    }

    $data_item->save();

    return redirect('master-items');
}

    public function delete($id)
    {
         $item = MasterItem::findOrFail($id);

    // Hapus file foto jika ada
    if (!empty($item->foto)) {
        $path = public_path('uploads/master_items/' . $item->foto);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    // Hapus data dari database
    $item->delete();

    return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach($data as $item)
        {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100,1000000);
            $item->laba = rand(10,99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi','Bukulapuk','TokoBagas','E Commurz','Blublu'];
        $random = rand(0,4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat','Alkes','Matkes','Umum','ATK'];
        $random = rand(0,4);
        return $array[$random];
    }

    public function exportExcel()
{
    return Excel::download(new MasterItemsExport, 'master-items.xlsx');
}
}
