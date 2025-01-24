<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;


class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('Pages.Kategori.index-kategori', compact('kategoris'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_kategori' => 'required|string|max:50|unique:tbl_kategori,nama_kategori',
        'alias_name' => 'required|string|max:4|unique:tbl_kategori,alias_name', // Fixed alias_name validation
    ]);

    try {
        Kategori::create($request->all());

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan kategori!');
    }
}


    public function edit(Kategori $kategori)
    {
        return response()->json($kategori);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:tbl_kategori,nama_kategori,' . $kategori->id,
            'alias_name' => 'required|string|max:4|unique:tbl_kategori,alias_name,' . $kategori->id,
        ]);

        $kategori->update($request->all());
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

