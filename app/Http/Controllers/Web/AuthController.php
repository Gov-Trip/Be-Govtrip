<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginView(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required|string',
        ]);

        $identifier = trim($credentials['email']);
        $password = $credentials['password'];

        // Determine email if user entered NIP or shorthand
        $user = User::where('email', $identifier)->first();
        if (! $user) {
            $employee = Employee::where('nip', $identifier)->first();
            if ($employee && $employee->user) {
                $user = $employee->user;
            }
        }

        if (! $user) {
            // Check fallback for seeded demo accounts
            $lower = strtolower($identifier);
            if (str_contains($lower, 'super') || str_contains($lower, 'root')) {
                $user = User::where('email', 'admin@gov.trip')->first();
            } elseif (str_contains($lower, 'bendahara') || str_contains($lower, 'keuangan')) {
                $user = User::where('email', 'bendahara@gov.trip')->first();
            } elseif (str_contains($lower, 'kadis')) {
                $user = User::where('email', 'kadis@gov.trip')->first();
            } elseif (str_contains($lower, 'manager') || str_contains($lower, 'kabid')) {
                $user = User::where('email', 'manager@gov.trip')->first();
            } elseif (str_contains($lower, 'atasan') || str_contains($lower, 'kasi')) {
                $user = User::where('email', 'atasan@gov.trip')->first();
            } elseif (str_contains($lower, 'admin') || str_contains($lower, 'travel') || str_contains($lower, 'tiket')) {
                $user = User::where('email', 'admintravel@gov.trip')->first();
            } else {
                $user = User::where('email', 'karyawan@gov.trip')->first();
            }
        }

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $deptCode = $user->employee?->department?->code;
            $grade = $user->employee?->grade;
            $position = strtolower($user->employee?->position ?? '');

            if ($deptCode === 'SUPER_ADMIN') {
                return redirect()->intended('/superadmin');
            } elseif ($deptCode === 'FINANCE') {
                return redirect()->intended('/bendahara');
            } elseif ($deptCode === 'ADMIN_TRAVEL') {
                return redirect()->intended('/admin');
            } elseif (in_array($grade, ['Manager', 'Director', 'Supervisor'])
                || str_contains($position, 'kepala')
                || str_contains($position, 'kadis')
                || str_contains($position, 'kabid')
                || str_contains($position, 'kasi')
                || str_contains($position, 'atasan')) {
                return redirect()->intended('/atasan');
            }

            return redirect()->intended('/pegawai');
        }

        return back()->withInput()->with('error', 'NIP / Email atau kata sandi yang Anda masukkan salah.');
    }

    public function registerView(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:employees,nip',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $user->id,
            'department_id' => $validated['department_id'],
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'position' => 'Staf Pegawai',
            'grade' => 'Staff',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/pegawai')->with('success', 'Akun berhasil didaftarkan!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
