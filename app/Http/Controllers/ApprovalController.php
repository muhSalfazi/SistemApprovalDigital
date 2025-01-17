<?php
namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Submission;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleNames = $user->roles->pluck('name'); // Ambil semua role user sebagai koleksi

        // Ambil data submission dengan approval terkait
        $submissions = Submission::with(['approvals.user.roles', 'user', 'departement'])
            ->when($roleNames->contains('prepared'), function ($query) use ($user) {
                $query->where('id_user', $user->id); // Filter berdasarkan user logged-in
            })
            ->get();

        return view('Pages.Approval.historyapprove', compact('submissions', 'roleNames'));
    }

    public function history($id_submission)
    {
        $user = auth()->user();
        $roleName = $user->role->name;
        $approvals = Approval::where('id_submission', $id_submission)
            ->with(['submission', 'user', 'submission.departement', 'submission.user'])
            ->orderBy('approved_date', 'asc')
            ->get();

        $submission = $approvals->first()?->submission;

        return view('Pages.Approval.historyperid', compact('approvals', 'submission'));
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
    public function getApprovalData($submissionId)
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name'); // Ambil semua role pengguna


        // Urutan role untuk approval
        $requiredApprovalOrder = ['Check1', 'Check2', 'approved'];

        // Temukan role pengguna dengan prioritas tertinggi
        $userRole = null;
        $currentRoleIndex = null;

        foreach ($userRoles as $role) {
            $index = array_search($role, $requiredApprovalOrder);
            if ($index !== false && ($currentRoleIndex === null || $index < $currentRoleIndex)) {
                $currentRoleIndex = $index;
                $userRole = $role;
            }
        }

        // Jika tidak ada role yang sesuai, larang akses
        if ($userRole === null) {
            return response()->json([
                'userRole' => null,
                'existingApproval' => false,
                'canApprove' => false,
            ]);
        }

        // Cek apakah approval pengguna sudah ada
        $existingApproval = Approval::where('id_submission', $submissionId)
            ->where('auditor_id', $user->id)
            ->exists();

        // Cek apakah semua approval sebelumnya selesai
        $allPreviousApproved = true;
        for ($i = 0; $i < $currentRoleIndex; $i++) {
            $roleToCheck = $requiredApprovalOrder[$i];
            $approvalExists = Approval::where('id_submission', $submissionId)
                ->whereHas('user.roles', function ($query) use ($roleToCheck) {
                    $query->where('name', $roleToCheck);
                })
                ->where('status', 'approved')
                ->exists();

            if (!$approvalExists) {
                $allPreviousApproved = false;
                break;
            }
        }

        // Jika approval sebelumnya belum selesai atau user sudah approve, form tidak akan muncul
        $canApprove = $allPreviousApproved && !$existingApproval;

        return response()->json([
            'userRole' => $userRole,
            'existingApproval' => $existingApproval,
            'canApprove' => $canApprove,
        ]);
    }

    public function getApprovalTable($submissionId)
    {
        try {
            $submission = Submission::with(['approvals.user.roles'])->findOrFail($submissionId);

            $approvalStages = ['Check1', 'Check2', 'Approved'];

            $approvals = collect($approvalStages)->map(function ($stage) use ($submission) {
                $approval = $submission->approvals->first(function ($a) use ($stage) {
                    return $a->user->roles->pluck('name')->contains($stage);
                });

                return [
                    'stage' => $stage,
                    'status' => $approval->status ?? 'Pending',
                    'approved_by' => $approval->user->name ?? '-',
                    'date' => $approval?->approved_date instanceof \Carbon\Carbon
                    ? $approval->approved_date->format('d M Y H:i:s')
                    : '-',
                    'remark' => $approval->remark ?? '-',
                ];
            });

            return response()->json([
                'approvals' => $approvals,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
