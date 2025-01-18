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

        // Ambil semua nama role pengguna
        $roleNames = $user->roles->pluck('name');

        if ($roleNames->isEmpty()) {
            abort(403, 'User does not have a role assigned.');
        }

        $userDepartmentId = $user->id_departement; // Departemen pengguna
        $allowedDepartments = [$userDepartmentId];

        // Query untuk menyesuaikan semua role yang dimiliki
        $submissions = Submission::with(['kategori', 'departement', 'user', 'approvals.user.roles'])
            ->when($roleNames->contains('prepared'), function ($query) use ($user, $allowedDepartments) {
                // Data untuk role 'prepared'
                $query->orWhere(function ($query) use ($user, $allowedDepartments) {
                    $query->where('id_user', $user->id)
                          ->whereIn('id_departement', $allowedDepartments);
                });
            })
            ->when($roleNames->contains('Check1'), function ($query) use ($userDepartmentId) {
                // Data untuk role 'Check1'
                $query->orWhere(function ($query) use ($userDepartmentId) {
                    $query->where(function ($query) use ($userDepartmentId) {
                        $query->whereDoesntHave('approvals', function ($subQuery) {
                            $subQuery->whereHas('user.roles', function ($roleQuery) {
                                $roleQuery->whereIn('name', ['Check1', 'Check2', 'approved']);
                            });
                        })
                        ->orWhereHas('approvals', function ($subQuery) {
                            $subQuery->whereNull('status'); // Approval dengan status null
                        });
                    })->where('id_departement', $userDepartmentId);
                });
            })
            ->when($roleNames->contains('Check2'), function ($query) use ($userDepartmentId) {
                // Data untuk role 'Check2'
                $query->orWhere(function ($query) use ($userDepartmentId) {
                    $query->whereHas('approvals', function ($subQuery) {
                        $subQuery->where('status', 'approved')
                                 ->whereHas('user.roles', function ($roleQuery) {
                                     $roleQuery->where('name', 'Check1');
                                 });
                    })
                    ->where('id_departement', $userDepartmentId)
                    ->whereDoesntHave('approvals', function ($subQuery) {
                        $subQuery->whereHas('user.roles', function ($roleQuery) {
                            $roleQuery->whereIn('name', ['Check2', 'approved']);
                        });
                    });
                });
            })
            ->when($roleNames->contains('approved'), function ($query) {
                // Data untuk role 'approved'
                $query->orWhere(function ($query) {
                    $query->whereHas('approvals', function ($subQuery) {
                        $subQuery->where('status', 'approved')
                                 ->whereHas('user.roles', function ($roleQuery) {
                                     $roleQuery->where('name', 'Check2');
                                 });
                    })
                    ->whereDoesntHave('approvals', function ($subQuery) {
                        $subQuery->whereHas('user.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'approved');
                        });
                    });
                });
            })
            ->get();

        return view('Pages.Approval.index-approval', compact('submissions', 'roleNames'));
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
