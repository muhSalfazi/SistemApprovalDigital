<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;


class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withTrashed()->get();
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
        if ($kategori->deleted_at !== null) {
            return redirect()->route('kategori.index')->with('error', 'Kategori non-aktif tidak dapat diperbarui. Silakan aktifkan kembali terlebih dahulu.');
        }
        
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:tbl_kategori,nama_kategori,' . $kategori->id,
            'alias_name' => 'required|string|max:4|unique:tbl_kategori,alias_name,' . $kategori->id,
        ]);

        $kategori->update($request->all());
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
         // Cek apakah departemen dalam keadaan non-aktif
         if ($kategori->deleted_at !== null) {
            return redirect()->route('kategori.index')->with('error', 'Kategori non-aktif tidak dapat diperbarui. Silakan aktifkan kembali terlebih dahulu.');
        }
        $kategori->forceDelete(); //delete permanen
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // status
    public function toggleStatus($id)
    {
        $kategori = Kategori::withTrashed()->findOrFail($id);

        if ($kategori->trashed()) {
            // Jika kategori non-aktif (soft deleted), maka aktifkan kembali
            $kategori->restore();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diaktifkan',
                'status' => 'Aktif',
                'deleted_at' => null
            ]);
        } else {
            // Jika kategori aktif, maka soft delete (non-aktifkan)
            $kategori->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dinonaktifkan',
                'status' => 'Nonaktif',
                'deleted_at' => now()->format('d-m-Y H:i')
            ]);
        }
    }


}

