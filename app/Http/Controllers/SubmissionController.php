<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Departement;
use App\Models\Kategori;

class SubmissionController extends Controller
{
    // Generate nomor transaksi
    public function generateTransactionNumber(Request $request)
    {
        \Log::info('Received request:', $request->all());

        $request->validate([
            'id_departement' => 'required|exists:tbl_departement,id',
            'id_kategori' => 'required|exists:tbl_kategori,id',
        ]);

        try {
            // Ambil submission terbaru berdasarkan nomor transaksi
            $latestSubmission = Submission::latest('created_at')->first();

            // Jika tidak ada submission sebelumnya, mulai dari 0
            $lastNumber = $latestSubmission ? intval(substr($latestSubmission->no_transaksi, -2)) : -1;

            // Tambahkan 1 hanya jika $lastNumber >= 0, jika -1 maka akan menjadi 0
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

            $currentMonth = date('m');
            $currentYear = date('y');

            // Format nomor transaksi
            // $no_transaksi = "MR" . str_pad($request->id_departement, 2, '0', STR_PAD_LEFT) . $currentMonth . $currentYear . $newNumber;
            $no_transaksi =  str_pad($request->id_departement, 2, '0', STR_PAD_LEFT) . $currentMonth . $currentYear . $newNumber;

            \Log::info('Generated transaction number:', ['no_transaksi' => $no_transaksi]);

            return response()->json(['no_transaksi' => $no_transaksi]);
        } catch (\Exception $e) {
            \Log::error('Error generating transaction number:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to generate transaction number.'], 500);
        }


    }
    //view
    public function create()
    {

        // Ambil data departemen dan kategori untuk dropdown
        $departements = Departement::all();
        $categories = Kategori::all();

        return view('Pages.Approval.create', compact('departements', 'categories'));
    }
    // Simpan submission
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_departement' => 'required|exists:tbl_departement,id',
            'id_kategori' => 'required|exists:tbl_kategori,id',
            'id_user' => 'required|exists:tbl_users,id',
            'title' => 'required|string|max:255',
            'no_transaksi' => 'required|string|unique:tbl_submission,no_transaksi',
            'remark' => 'nullable|string',
            'lampiran_pdf' => 'required|mimes:pdf|max:2048',
        ]);

        \Log::info('Received request data:', $request->all());

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('lampiran_pdf')) {
            $pdfFile = $request->file('lampiran_pdf');
            $fileName = $request->no_transaksi . '.' . $pdfFile->getClientOriginalExtension();

            // Simpan file di direktori public/submissions
            $destinationPath = public_path('submissions');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true); // Buat folder jika belum ada
            }
            $pdfFile->move($destinationPath, $fileName);

            // Simpan path relatif ke database
            $filePath = 'submissions/' . $fileName;

            \Log::info('File path:', ['path' => $filePath]);
        }

        // Simpan data ke database
        Submission::create([
            'id_departement' => $request->id_departement,
            'id_kategori' => $request->id_kategori,
            'id_user' => $request->id_user,
            'title' => $request->title,
            'no_transaksi' => $request->no_transaksi,
            'remark' => $request->remark,
            'lampiran_pdf' => $filePath,
        ]);

        return redirect()->route('submissions.index')->with('success', 'Submission berhasil dibuat.');
    }



    // Tampilkan daftar submission
    public function index()
    {
        $user = auth()->user();
        $role = $user->role; // Asumsikan Anda memiliki atribut 'role' di tabel pengguna

        // Data awal untuk role 'prepared'
        $submissions = Submission::with(['kategori', 'departement', 'user', 'approvals'])
            ->when($role === 'prepared', function ($query) {
                return $query; // Prepared melihat semua data
            })
            ->when($role === 'Check 1', function ($query) {
                return $query->whereHas('approvals', function ($subQuery) {
                    $subQuery->where('status', 'approved')->where('auditor_id', auth()->id());
                });
            })
            ->when($role === 'Check 2', function ($query) {
                return $query->whereHas('approvals', function ($subQuery) {
                    $subQuery->where('status', 'approved')->where('auditor_id', auth()->id());
                });
            })
            ->when($role === 'Approved', function ($query) {
                return $query->whereDoesntHave('approvals', function ($subQuery) {
                    $subQuery->where('status', 'rejected');
                });
            })
            ->when($role === 'viewer', function ($query) {
                return $query->whereHas('approvals', function ($subQuery) {
                    $subQuery->where('status', 'approved');
                });
            })
            ->get();

        return view('Pages.Approval.index-approval', compact('submissions'));
    }


    // Download file PDF
   public function download($id)
    {
        $submission = Submission::findOrFail($id);
        $filePath = public_path($submission->lampiran_pdf);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }
}
