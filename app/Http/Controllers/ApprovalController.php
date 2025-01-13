<?php
namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();
    $roleName = $user->role->name; // Nama role user yang sedang login

    // Ambil data approval berdasarkan role
    $approvals = Approval::select('tbl_approval.*')
    ->join('tbl_submission', 'tbl_approval.id_submission', '=', 'tbl_submission.id')
    ->with(['submission', 'user', 'submission.departement', 'submission.user'])
    ->when($roleName === 'prepared', function ($query) use ($user) {
        $query->whereHas('submission', function ($subQuery) use ($user) {
            $subQuery->where('id_user', $user->id);
        });
    })
    ->when($roleName === 'viewer', function ($query) {
        $query->where('status', 'approved')->whereHas('user.role', function ($roleQuery) {
            $roleQuery->where('name', 'approvalManager');
        });
    })
    // ->orderBy('tbl_approval.approved_date', 'desc') // Urutkan berdasarkan tanggal persetujuan
    ->orderBy('tbl_submission.no_transaksi', 'asc') // Urutkan berdasarkan nomor transaksi
    ->get();


    // Return view dengan data approvals
    return view('Pages.Approval.historyapprove', compact('approvals', 'roleName'));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_submission' => 'required|exists:tbl_submission,id',
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string',
        ]);

        if ($validated['status'] === 'rejected' && empty($validated['remark'])) {
            return response()->json([
                'error' => 'Remark wajib diisi jika status adalah NO.'
            ], 422);
        }

        try {
            Approval::create([
                'id_submission' => $validated['id_submission'],
                'auditor_id' => auth()->id(),
                'status' => $validated['status'],
                'approved_date' => now(),
                'remark' => $validated['remark'] ?? null,
            ]);

            return response()->json([
                'success' => 'Submission berhasil diproses.'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error submitting approval: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memproses submission. Silakan coba lagi.'
            ], 500);
        }
    }

}
