<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department', 'supervisor'])->latest()->get();

        return response()->json(['success' => true, 'data' => $employees]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'nip' => 'required|unique:employees,nip',
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'grade' => 'required|string|max:50',
            'direct_supervisor_id' => 'nullable|exists:employees,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'nip' => $request->nip,
                'name' => $request->name,
                'position' => $request->position,
                'grade' => $request->grade,
                'direct_supervisor_id' => $request->direct_supervisor_id,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pegawai ditambahkan', 'data' => $employee->load('user')], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => Employee::with(['user', 'department'])->findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'department_id' => 'required|exists:departments,id',
            'nip' => 'required|unique:employees,nip,'.$employee->id,
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'grade' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'email' => $request->email,
                'is_active' => $request->is_active ?? $user->is_active,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $employee->update($request->only(['department_id', 'nip', 'name', 'position', 'grade', 'direct_supervisor_id']));

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data pegawai diperbarui', 'data' => $employee]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->user()->update(['is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Akun pegawai dinonaktifkan.']);
    }

    // PATCH /api/super-admin/employees/{id}/toggle-status
    public function toggleStatus(Request $request, $id)
    {
        try {
            $employee = Employee::with('user')->findOrFail($id);

            if (! $employee->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai ini belum memiliki akun pengguna terkait.',
                ], 404);
            }

            if ($employee->user->id === $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menonaktifkan akun Super Admin yang sedang digunakan.',
                ], 400);
            }

            $employee->user->is_active = ! $employee->user->is_active;
            $employee->user->save();

            $statusLabel = $employee->user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            AuditLog::create([
                'user_id'     => $request->user()->id,
                'action'      => 'TOGGLE_USER_STATUS',
                'module'      => 'SUPER_ADMIN',
                'description' => "Akun {$employee->name} berhasil {$statusLabel}.",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status akun {$employee->name} berhasil {$statusLabel}.",
                'data'    => [
                    'employee_id' => $employee->id,
                    'user_id'     => $employee->user->id,
                    'is_active'   => (bool) $employee->user->is_active,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
}
