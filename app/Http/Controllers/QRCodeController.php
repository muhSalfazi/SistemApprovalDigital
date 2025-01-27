<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Zxing\QrReader;


class QRCodeController extends Controller
{
    public function showqr()
    {
        return view('auth.validasi-qrcode');
    }

    public function validateQRCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        \Log::info('QR Code Received:', ['qr_code' => $request->qr_code]);

        try {
            // Dekripsi QR Code
            $decryptedData = Crypt::decryptString(trim($request->qr_code));
            \Log::info('Decrypted QR Code:', ['decrypted_data' => $decryptedData]);

            // Pecah string QR Code berdasarkan delimiter "|"
            $parts = explode('|', $decryptedData);

            if (count($parts) !== 4) {
                return redirect()->route('validate.qrcode')->with('error', 'QR Code tidak valid.');
            }

            $role = trim($parts[0]);  // Bisa 'Prepare', 'Check1', 'Check2', atau 'Approved'
            $noTransaksi = trim($parts[1]);
            $approvedBy = trim($parts[2]);

            \Log::info('Parsed QR Code Data:', [
                'role' => $role,
                'no_transaksi' => $noTransaksi,
                'approved_by' => $approvedBy
            ]);

            // Periksa apakah approved_by memiliki nilai 'Pending'
            if (strcasecmp($approvedBy, 'Pending') === 0) {
                return redirect()->route('validate.qrcode')->with('error', 'Dokumen belum di-approve. Harap menunggu proses persetujuan.');
            }

            // Cek apakah nomor transaksi tersedia di database
            $submission = Submission::where('no_transaksi', $noTransaksi)->first();

            // List peran yang diizinkan
            $validRoles = ['Prepare', 'Check1', 'Check2', 'Approved'];

            if ($submission && in_array($role, $validRoles)) {
                return redirect()->route('validate.qrcode')->with('success', "Validasi Berhasil! Peran: {$role}, No.Doc: {$noTransaksi}, Disetujui oleh: {$approvedBy}");
            } else {
                return redirect()->route('validate.qrcode')->with('error', 'Data QR Code tidak dikenali.');
            }

        } catch (\Exception $e) {
            \Log::error('Error while processing QR Code:', ['error' => $e->getMessage()]);
            return redirect()->route('validate.qrcode')->with('error', 'Terjadi kesalahan saat membaca QR Code.');
        }
    }

    public function uploadQRCode(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Baca file gambar langsung dari request tanpa menyimpannya ke server
            $image = $request->file('qr_image');

            // Gunakan ZXing untuk membaca QR Code dari file yang diunggah
            $qrcodeReader = new \Zxing\QrReader($image->getRealPath());
            $decodedText = $qrcodeReader->text();

            if ($decodedText) {
                \Log::info('QR Code Terbaca:', ['qr_code' => $decodedText]);

                try {
                    // Dekripsi QR Code yang telah di-scan
                    $decryptedData = Crypt::decryptString(trim($decodedText));

                    // Pecah string QR Code berdasarkan delimiter "|"
                    $parts = explode('|', $decryptedData);

                    if (count($parts) !== 4) {
                        return response()->json(['success' => false, 'message' => 'QR Code tidak valid.']);
                    }

                    $role = trim($parts[0]);
                    $noTransaksi = trim($parts[1]);
                    $approvedBy = trim($parts[2]);

                    \Log::info('Parsed QR Code Data:', [
                        'role' => $role,
                        'no_transaksi' => $noTransaksi,
                        'approved_by' => $approvedBy
                    ]);

                    // Cek apakah status disetujui adalah "Pending"
                    if (strcasecmp($approvedBy, 'Pending') === 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Dokumen belum di-approve. Harap menunggu proses persetujuan.'
                        ]);
                    }

                    // Cek apakah nomor transaksi tersedia di database
                    $submission = Submission::where('no_transaksi', $noTransaksi)->first();

                    $validRoles = ['Prepare', 'Check1', 'Check2', 'Approved'];

                    if ($submission && in_array($role, $validRoles)) {
                        return response()->json([
                            'success' => true,
                            'message' => "Validasi Berhasil! Peran: {$role}, No.Doc: {$noTransaksi}, Disetujui oleh: {$approvedBy}"
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data QR Code tidak dikenali.'
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::error('Decryption Error:', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code tidak valid atau terenkripsi salah.'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak dapat dibaca.'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error while processing QR Code:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses gambar.'
            ]);
        }
    }

}
