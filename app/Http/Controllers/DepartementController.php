<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departement;

class DepartementController extends Controller
{
    //
    public function index()
    {
        $Departments = Departement::withTrashed()->get();
        return view('Pages.Departement.index-departement', compact('Departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departement' => 'required|string|max:4|unique:tbl_departement,nama_departement',
            'deksripsi' => 'required|string|unique:tbl_departement,deksripsi',
        ]);

        try {
            Departement::create($request->all());

            return redirect()->route('departement.index')->with('success', 'Departement berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan Department!');
        }
    }

    public function edit(Departement $departement)
    {
        return response()->json($departement);
    }

    public function update(Request $request, Departement $departement)
    {
        // Cek apakah departemen dalam keadaan non-aktif
        if ($departement->deleted_at !== null) {
            return redirect()->route('departement.index')->with('error', 'Departemen non-aktif tidak dapat diperbarui. Silakan aktifkan kembali terlebih dahulu.');
        }

        // Validasi input
        $request->validate([
            'nama_departement' => 'required|string|max:10|unique:tbl_departement,nama_departement,' . $departement->id,
            'deksripsi' => 'required|string|unique:tbl_departement,deksripsi,' . $departement->id,
        ]);

        // Update departemen jika valid
        $departement->update($request->all());

        return redirect()->route('departement.index')->with('success', 'Departemen berhasil diperbarui.');
    }
    public function destroy(Departement $departement)
    {
        // Cek apakah departemen dalam keadaan non-aktif
        if ($departement->deleted_at !== null) {
            return redirect()->route('departement.index')->with('error', 'Departemen non-aktif tidak dapat dihapus. Silakan aktifkan kembali terlebih dahulu.');
        }

        // Hapus departemen permanen
        $departement->forceDelete();

        return redirect()->route('departement.index')->with('success', 'Departemen berhasil dihapus.');
    }


    // status
    public function toggleStatus($id)
    {

        $departement = Departement::withTrashed()->findOrFail($id);

        if ($departement->trashed()) {
            $departement->restore();
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil diaktifkan',
                'deleted_at' => null
            ]);
        } else {
            $departement->delete();
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil dinonaktifkan',
                'deleted_at' => now()->format('d-m-Y H:i')
            ]);
        }
    }


}
