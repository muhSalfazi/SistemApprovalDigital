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

        // Cek apakah user memiliki role superadmin
        $isSuperAdmin = $roleNames->contains('superadmin');

        $submissions = Submission::with(['approvals.user.roles', 'user', 'departement'])
            ->when(!$isSuperAdmin, function ($query) use ($user, $roleNames) {
                $query->when($roleNames->contains('prepared'), function ($query) use ($user) {
                    $query->where('id_user', $user->id); // Filter berdasarkan user logged-in
                })
                    ->when($roleNames->contains('viewer'), function ($query) {
                        $query->whereHas('approvals', function ($subQuery) {
                            $subQuery->whereHas('user.roles', function ($roleQuery) {
                                $roleQuery->where('name', 'approved'); // Pastikan submission telah disetujui
                            })->where('status', 'approved'); // Status approval harus 'approved'
                        });
                    });
            }) // Jika superadmin, tidak ada filter tambahan
            ->get();

        return view('Pages.Approval.historyapprove', compact('submissions', 'roleNames'));
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
        $userRoles = $user->roles->pluck('name')->toArray(); // Ambil semua role pengguna

        // Urutan role untuk approval (berdasarkan prioritas)
        $requiredApprovalOrder = ['Check1', 'Check2', 'approved'];

        // Ambil semua approval yang sudah dilakukan pada submission ini
        $existingApprovals = Approval::where('id_submission', $submissionId)
            ->whereHas('user.roles', function ($query) use ($requiredApprovalOrder) {
                $query->whereIn('name', $requiredApprovalOrder);
            })
            ->get()
            ->groupBy(function ($approval) {
                return $approval->user->roles->pluck('name')->toArray();
            });

        // Temukan semua role yang dimiliki pengguna yang sesuai dengan urutan approval
        $matchingRoles = array_intersect($requiredApprovalOrder, $userRoles);

        if (empty($matchingRoles)) {
            return response()->json([
                'userRole' => null,
                'existingApproval' => false,
                'canApprove' => false,
                'approvedApprovals' => [],
            ]);
        }

        // Pilih role tertinggi dari role yang dimiliki pengguna
        $userHighestRole = null;
        foreach ($requiredApprovalOrder as $role) {
            if (in_array($role, $matchingRoles)) {
                $userHighestRole = $role;
            }
        }

        // Cek apakah pengguna telah menyetujui dengan salah satu role tertinggi yang mereka miliki
        $roleAlreadyApproved = collect($matchingRoles)->contains(function ($role) use ($existingApprovals) {
            return $existingApprovals->has($role) &&
                strtolower($existingApprovals[$role]->first()->status) === 'approved';
        });

        // Pastikan approval sebelumnya sudah selesai dan semua berstatus 'approved'
        $currentRoleIndex = array_search($userHighestRole, $requiredApprovalOrder);
        $allPreviousApproved = true;

        for ($i = 0; $i < $currentRoleIndex; $i++) {
            $previousRole = $requiredApprovalOrder[$i];

            // Jika approval sebelumnya belum ada, approval belum selesai
            if (!$existingApprovals->has($previousRole)) {
                $allPreviousApproved = false;
                break;
            }

            $approvalStatus = strtolower($existingApprovals[$previousRole]->first()->status);

            // Jika approval sebelumnya berstatus 'rejected', form tidak boleh ditampilkan
            if ($approvalStatus === 'rejected') {
                return response()->json([
                    'userRole' => $userHighestRole,
                    'existingApproval' => true,
                    'canApprove' => false,
                ]);
            }

            // Simpan semua role dengan status "approved"
            if ($approvalStatus === 'approved') {
                foreach ($existingApprovals[$previousRole] as $approval) {
                    $approvedApprovals[] = [
                        'role' => $previousRole,
                        'status' => $approvalStatus,
                        'approved_by' => $approval->user->name
                    ];
                }
            } else {
                $allPreviousApproved = false;
                break;
            }
        }

        // Cek apakah pengguna merupakan satu-satunya yang memiliki role tertinggi yang harus approve
        $isOnlyApprover = !Approval::where('id_submission', $submissionId)
            ->whereHas('user.roles', function ($query) use ($userHighestRole) {
                $query->where('name', $userHighestRole);
            })
            ->exists();

        // Form akan muncul jika semua approval sebelumnya berstatus 'approved' dan role tertinggi belum approve
        $canApprove = ($allPreviousApproved && !$roleAlreadyApproved) || $isOnlyApprover;

        return response()->json([
            'userRoles' => $matchingRoles,  // Semua role yang dimiliki user sesuai prioritas
            'userHighestRole' => $userHighestRole, // Role tertinggi yang bisa approve
            'existingApproval' => $roleAlreadyApproved, // Apakah user sudah approve dengan role tertinggi
            'canApprove' => $canApprove, // Apakah form harus ditampilkan
        ]);
    }


    public function getApprovalTable($submissionId)
    {
        try {
            $submission = Submission::with(['approvals.user.roles'])->findOrFail($submissionId);

            $approvalStages = ['Check1', 'Check2', 'approved'];

            // Koleksi untuk menyimpan approval unik berdasarkan role
            $uniqueApprovals = collect();

            $approvals = collect($approvalStages)->map(function ($stage) use ($submission, $uniqueApprovals) {
                // Ambil approvals yang terkait dengan role tertentu
                $filteredApprovals = $submission->approvals->filter(function ($a) use ($stage) {
                    return $a->user->roles->pluck('name')->contains($stage);
                });

                if ($filteredApprovals->isEmpty()) {
                    return [
                        'stage' => $stage,
                        'status' => 'Pending',
                        'approved_by' => '-',
                        'date' => '-',
                        'remark' => '-',
                    ];
                }

                // Hanya menyimpan satu approval per stage berdasarkan tanggal approval paling awal
                $filteredApprovals->each(function ($approval) use ($stage, $uniqueApprovals) {
                    // Pastikan role tertentu belum ada dalam koleksi
                    if (!$uniqueApprovals->has($stage)) {
                        $uniqueApprovals->put($stage, [
                            'stage' => $stage,
                            'status' => $approval->status ?? 'Pending',
                            'approved_by' => $approval->user->name ?? '-',
                            'date' => $approval->approved_date
                                ? $approval->approved_date->format('d M Y H:i:s')
                                : '-',
                            'remark' => $approval->remark ?? '-',
                        ]);
                    }
                });

                return $uniqueApprovals->get($stage);
            });

            return response()->json([
                'approvals' => $approvals->values(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching data: ' . $e->getMessage(),
            ], 500);
        }
    }

}
