<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Departement;
use App\Models\Kategori;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\View;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\Crypt;

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
    public function downloadWithQRCode($id)
    {
        $submission = Submission::with(['approvals.user.roles'])->findOrFail($id);
        $filePath = public_path($submission->lampiran_pdf);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Urutan approval
        $approvalStages = ['prepare', 'Check1', 'Check2', 'approved'];

        $approvals = [];
        $approvalTimes = [];

        // Ambil waktu prepare berdasarkan create_at submission
        $approvals['prepare'] = $submission->user->name ?? 'Pending';
        $approvalTimes['prepare'] = $submission->created_at ? $submission->created_at->format('d M Y H:i:s') : 'Pending';

        // Ambil approval berdasarkan role yang dimiliki pengguna
        foreach (['Check1', 'Check2', 'approved'] as $stage) {
            $approval = $submission->approvals->first(function ($approval) use ($stage) {
                return $approval->user->roles->pluck('name')->contains($stage);
            });

            if ($approval) {
                $approvals[$stage] = $approval->user->name;
                $approvalTimes[$stage] = $approval->approved_date ? $approval->approved_date->format('d M Y H:i:s') : 'Pending';
            } else {
                $approvals[$stage] = 'Pending';
                $approvalTimes[$stage] = 'Pending';
            }
        }

        // Fungsi untuk generate QR Code menggunakan Endroid QR Code
        function generateQrCode($content)
        {
            return Builder::create()
                ->writer(new PngWriter())
                ->data(Crypt::encryptString($content))
                ->encoding(new Encoding('UTF-8'))
                ->size(250) // QR code lebih besar untuk profesional tampilan
                ->margin(10)
                ->build()
                ->getString();
        }

        // Generate QR Codes
        $qrCodes = [
            'prepare'  => base64_encode(generateQrCode("Prepare: {$submission->no_transaksi} - {$approvals['prepare']} - {$approvalTimes['prepare']}")),
            'check1'   => base64_encode(generateQrCode("Check-1: {$submission->no_transaksi} - {$approvals['Check1']} - {$approvalTimes['Check1']}")),
            'check2'   => base64_encode(generateQrCode("Check-2: {$submission->no_transaksi} - {$approvals['Check2']} - {$approvalTimes['Check2']}")),
            'approved' => base64_encode(generateQrCode("Approved: {$submission->no_transaksi} - {$approvals['approved']} - {$approvalTimes['approved']}")),
        ];

        // Generate halaman QR Code sebagai HTML
        $html = View::make('pdf.qrcode', compact('submission', 'qrCodes', 'approvals', 'approvalTimes'))->render();

        // Create PDF using GD instead of Imagick
        $mpdf = new Mpdf([
            'useGD' => true,
            'mode' => 'utf-8',
            'format' => 'A4',
        ]);

        $mpdf->AddPage(); 
        $mpdf->WriteHTML($html);

        $pageCount = $mpdf->SetSourceFile($filePath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $mpdf->ImportPage($i);
            $mpdf->AddPage();
            $mpdf->UseTemplate($tplId);
        }

        // Output PDF
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'approved-system-kbi.pdf');
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
