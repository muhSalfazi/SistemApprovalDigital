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
        \Log::info('Received encrypted request:', $request->all());

        try {
            // Dekripsi ID departemen dan kategori
            $decryptedDepartementId = decrypt($request->id_departement);
            $decryptedKategoriId = decrypt($request->id_kategori);

            // Validasi data setelah didekripsi
            $request->merge([
                'id_departement' => $decryptedDepartementId,
                'id_kategori' => $decryptedKategoriId,
            ]);

            $request->validate([
                'id_departement' => 'required|exists:tbl_departement,id',
                'id_kategori' => 'required|exists:tbl_kategori,id',
            ]);

            // Ambil alias_name kategori berdasarkan id_kategori yang sudah didekripsi
            $kategori = Kategori::findOrFail($decryptedKategoriId);
            $aliasName = strtoupper($kategori->alias_name);

            // Ambil submission terbaru berdasarkan departemen
            $latestSubmission = Submission::where('id_departement', $decryptedDepartementId)
                ->latest('created_at')
                ->first();

            // Jika tidak ada submission sebelumnya, mulai dari 00
            $lastNumber = $latestSubmission ? intval(substr($latestSubmission->no_transaksi, -2)) : -1;
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

            $currentMonth = date('m');
            $currentYear = date('y');

            // Format nomor transaksi dengan alias_name kategori di depan
            $no_transaksi = $aliasName . '-' . str_pad($decryptedDepartementId, 2, '0', STR_PAD_LEFT) . $currentMonth . $currentYear . $newNumber;

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
        // Ambil pengguna yang sedang login
        $user = auth()->user();

        // Ambil departemen berdasarkan id yang dimiliki user (dengan pengecualian 'ALL')
        $departements = Departement::where('nama_departement', '!=', 'ALL')
            ->where('id', $user->id_departement)
            ->get();

        // Ambil kategori berdasarkan id yang dimiliki user
        $categories = Kategori::where('id', $user->id_kategori)->get();

        return view('Pages.Approval.create', compact('departements', 'categories'));
    }


    // Simpan submission
    public function store(Request $request)
    {
        try {
            // Dekripsi nilai departemen dan kategori sebelum validasi
            $idDepartement = decrypt($request->input('id_departement'));
            $idKategori = decrypt($request->input('id_kategori'));

            // Validasi data
            $request->merge([
                'id_departement' => $idDepartement,
                'id_kategori' => $idKategori,
            ]);

            $request->validate([
                'id_departement' => 'required|exists:tbl_departement,id',
                'id_kategori' => 'required|exists:tbl_kategori,id',
                'title' => 'required|string|max:255',
                'no_transaksi' => 'required|string',
                'remark' => 'required|string|max:500',
                'lampiran_pdf' => 'required|mimes:pdf|max:5120',
            ]);

            \Log::info('Request Data:', $request->all());

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
                'id_departement' => $idDepartement,
                'id_kategori' => $idKategori,
                'id_user' => auth()->user()->id,
                'title' => $request->title,
                'no_transaksi' => $request->no_transaksi,
                'remark' => $request->remark,
                'lampiran_pdf' => $filePath,
            ]);

            return redirect()->route('submissions.index')->with('success', 'Submission berhasil disimpan.');

        } catch (\Exception $e) {
            \Log::error('Error saving submission:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }

    // Tampilkan daftar submission
    public function index()
    {
        $user = auth()->user()->load('roles', 'kategori'); // Load eager untuk menghindari null
        $roleNames = $user->roles->pluck('name');

        if ($roleNames->isEmpty()) {
            abort(403, 'User does not have a role assigned.');
        }

        // Periksa apakah user adalah superadmin
        $isSuperAdmin = $roleNames->contains('superadmin');

        // Ambil ID departemen dan kategori pengguna
        $userDepartmentId = $user->id_departement;
        $userCategoryId = $user->id_kategori;

        // Query untuk menyesuaikan semua role yang dimiliki
        $submissions = Submission::with(['kategori', 'departement', 'user', 'approvals.user.roles'])
            ->when(!$isSuperAdmin && $roleNames->contains('prepared'), function ($query) use ($user) {
                // Data untuk role 'prepared', hanya untuk submission yang dibuat oleh pengguna ini dan sesuai dengan departemen/kategori mereka
                $query->where('id_user', $user->id)
                    ->where('id_departement', $user->id_departement)
                    ->where('id_kategori', $user->id_kategori);
            })
            ->when(!$isSuperAdmin && $roleNames->contains('Check1'), function ($query) use ($userDepartmentId, $userCategoryId) {
                // Data untuk role 'Check1', hanya untuk departemen dan kategori tertentu
                $query->orWhere(function ($query) use ($userDepartmentId, $userCategoryId) {
                    $query->where(function ($query) {
                        $query->whereDoesntHave('approvals', function ($subQuery) {
                            $subQuery->whereHas('user.roles', function ($roleQuery) {
                                $roleQuery->whereIn('name', ['Check1', 'Check2', 'approved']);
                            });
                        })
                            ->orWhereHas('approvals', function ($subQuery) {
                                $subQuery->whereNull('status');
                            });
                    })
                        ->where('id_departement', $userDepartmentId)
                        ->where('id_kategori', $userCategoryId);
                });
            })
            ->when(!$isSuperAdmin && $roleNames->contains('Check2'), function ($query) use ($userDepartmentId, $userCategoryId) {
                // Data untuk role 'Check2', hanya untuk departemen dan kategori tertentu
                $query->orWhere(function ($query) use ($userDepartmentId, $userCategoryId) {
                    $query->whereHas('approvals', function ($subQuery) {
                        $subQuery->where('status', 'approved')
                            ->whereHas('user.roles', function ($roleQuery) {
                                $roleQuery->where('name', 'Check1');
                            });
                    })
                        ->where('id_departement', $userDepartmentId)
                        ->where('id_kategori', $userCategoryId)
                        ->whereDoesntHave('approvals', function ($subQuery) {
                            $subQuery->whereHas('user.roles', function ($roleQuery) {
                                $roleQuery->whereIn('name', ['Check2', 'approved']);
                            });
                        });
                });
            })
            ->when(!$isSuperAdmin && $roleNames->contains('approved'), function ($query) {
                // Data untuk role 'approved', melihat semua departemen dan kategori, hanya jika sudah di-approve oleh Check2
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
            ->when($isSuperAdmin, function ($query) {
                // Jika superadmin, tampilkan semua data tanpa batasan departemen atau kategori
                $query->orWhereNotNull('id');
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

        // Ambil waktu prepare berdasarkan created_at submission
        $approvals['prepare'] = $submission->user->name ?? 'Pending';
        $approvalTimes['prepare'] = $submission->created_at ? $submission->created_at->format('d M Y H:i:s') : 'Pending';

        // Ambil approval berdasarkan role pengguna
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
            $encryptedData = Crypt::encryptString($content);  // Enkripsi data
            return Builder::create()
                ->writer(new PngWriter())
                ->data($encryptedData)  // QR code berisi data terenkripsi
                ->encoding(new Encoding('UTF-8'))
                ->size(400)  // Ukuran diperbesar agar lebih mudah terbaca
                ->margin(20)  // Margin untuk akurasi
                ->build()
                ->getString();
        }

        // Encoding base64 dilakukan di sini
        $qrCodes = [
            'prepare' => base64_encode(generateQrCode("Prepare|{$submission->no_transaksi}|{$approvals['prepare']}|{$approvalTimes['prepare']}")),
            'check1'  => base64_encode(generateQrCode("Check1|{$submission->no_transaksi}|{$approvals['Check1']}|{$approvalTimes['Check1']}")),
            'check2'  => base64_encode(generateQrCode("Check2|{$submission->no_transaksi}|{$approvals['Check2']}|{$approvalTimes['Check2']}")),
            'approved'=> base64_encode(generateQrCode("Approved|{$submission->no_transaksi}|{$approvals['approved']}|{$approvalTimes['approved']}")),
        ];





        // Generate halaman QR Code sebagai HTML
        $html = View::make('pdf.qrcode', compact('submission', 'qrCodes', 'approvals', 'approvalTimes'))->render();

        // Inisialisasi MPDF dengan pengaturan otomatis orientasi
        $mpdf = new Mpdf([
            'useGD' => true,
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P', // Default portrait
            'autoPageBreak' => true,
        ]);

        // Tambahkan halaman QR code
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);

        $pageCount = $mpdf->SetSourceFile($filePath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $mpdf->ImportPage($i);

            // Deteksi orientasi halaman
            $size = $mpdf->GetTemplateSize($tplId);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $mpdf->AddPage($orientation);
            $mpdf->UseTemplate($tplId);
        }

        $fileName = 'approved_' . str_replace('/', '-', $submission->no_transaksi) . '-kBI.pdf';
        // Output PDF
        return response($mpdf->Output($fileName, Destination::INLINE))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"$fileName\"")
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
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
