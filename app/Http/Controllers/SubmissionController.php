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
        ]);

        try {
            // Ambil submission terbaru berdasarkan departemen
            $latestSubmission = Submission::where('id_departement', $request->id_departement)
                ->latest('created_at')
                ->first();

            // Jika tidak ada submission sebelumnya untuk departemen ini, mulai dari 00
            $lastNumber = $latestSubmission ? intval(substr($latestSubmission->no_transaksi, -2)) : -1;

            // Tambahkan 1 jika $lastNumber >= 0, jika -1 maka akan menjadi 00
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

            $currentMonth = date('m');
            $currentYear = date('y');

            // Format nomor transaksi berdasarkan departemen
            $no_transaksi = str_pad($request->id_departement, 2, '0', STR_PAD_LEFT) . $currentMonth . $currentYear . $newNumber;

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
        // Ambil data departemen kecuali yang bernama 'ALL'
        $departements = Departement::where('nama_departement', '!=', 'ALL')->get();

        // Ambil semua data kategori
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
            'no_transaksi' => 'required|string',
            'remark' => 'required|string|max:200',
            'lampiran_pdf' => 'required|mimes:pdf|max:5120',
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

        try {
            Submission::create([
                'id_departement' => $request->id_departement,
                'id_kategori' => $request->id_kategori,
                'id_user' => $request->id_user,
                'title' => $request->title,
                'no_transaksi' => $request->no_transaksi,
                'remark' => $request->remark,
                'lampiran_pdf' => $filePath,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving submission:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }

        return redirect()->route('submissions.index')->with('success', 'Submission berhasil dibuat.');
    }

    // Tampilkan daftar submission
    public function index()
    {
        $user = auth()->user();
        $roleName = $user->role->name; // Ambil nama role dari relasi role
        $userDepartmentId = $user->id_departement; // Departemen pengguna

        $allowedDepartments = [$userDepartmentId];
        $submissions = Submission::with(['kategori', 'departement', 'user', 'approvals'])
        ->when($roleName === 'prepared', function ($query) use ($user, $allowedDepartments) {
            // Filter submission berdasarkan user yang login
            return $query->where('id_user', $user->id)
                ->whereIn('id_departement', $allowedDepartments); // Filter berdasarkan departemen yang diizinkan
        })

            ->when($roleName === 'Check1', function ($query) use ($userDepartmentId) {
                // Check1 hanya melihat submission sesuai departemen dan belum diproses
                return $query->where(function ($query) use ($userDepartmentId) {
                    $query->whereDoesntHave('approvals') // Tidak ada data di tabel approvals
                        ->orWhereHas('approvals', function ($subQuery) {
                            $subQuery->whereNull('status'); // Jika ada, pastikan status masih null
                        });
                })->where('id_departement', $userDepartmentId);
            })
            ->when($roleName === 'Check2', function ($query) use ($userDepartmentId) {
                // Check2 hanya melihat submission yang sudah disetujui oleh Check1
                // dan belum memiliki approval oleh Check2 pada departemen atau submission tertentu
                return $query->whereHas('approvals', function ($subQuery) {
                    $subQuery->where('status', 'approved') // Disetujui oleh Check1
                        ->whereHas('user', function ($userQuery) {
                            $userQuery->whereHas('role', function ($roleQuery) {
                                $roleQuery->where('name', 'Check1'); // Role Check1
                            });
                        });
                })
                    ->where('id_departement', $userDepartmentId) // Hanya untuk departemen pengguna
                    ->whereDoesntHave('approvals', function ($subQuery) {
                    $subQuery->whereHas('user', function ($userQuery) {
                        $userQuery->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('name', 'Check2'); // Sudah ada approval oleh Check2
                        });
                    });
                });
            })
            ->when($roleName === 'approvalManager', function ($query) {
                // approvalManager melihat semua submission yang disetujui oleh Check2
                // dan belum diproses oleh approvalManager
                return $query->whereHas('approvals', function ($subQuery) {
                    $subQuery->where('status', 'approved') // Disetujui oleh Check2
                        ->whereHas('user', function ($userQuery) {
                            $userQuery->whereHas('role', function ($roleQuery) {
                                $roleQuery->where('name', 'Check2'); // Role Check2
                            });
                        });
                })
                ->whereDoesntHave('approvals', function ($subQuery) {
                    $subQuery->whereHas('user', function ($userQuery) {
                        $userQuery->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('name', 'approvalManager'); // Sudah ada approval oleh approvalManager
                        });
                    });
                });
            })

            ->get();

        return view('Pages.Approval.index-approval', compact('submissions', 'roleName'));
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

    public function destroy($id)
    {
        try {
            // Cari submission berdasarkan ID
            $submission = Submission::findOrFail($id);

            // Hapus file PDF jika ada
            if ($submission->lampiran_pdf && file_exists(public_path($submission->lampiran_pdf))) {
                unlink(public_path($submission->lampiran_pdf));
            }

            // Hapus submission dari database
            $submission->delete();

            return redirect()->route('submissions.index')->with('success', 'Submission berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting submission:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus submission.']);
        }
    }
}
