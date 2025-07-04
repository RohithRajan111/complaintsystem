<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Dept;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;
        $remember = $request->boolean('remember');

        $admin = Admin::where('Admin_email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Auth::guard('admins')->login($admin, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('showadmin.dashboard'));
        }

        $dept = Dept::where('Dept_email', $email)->first();
        if ($dept && Hash::check($password, $dept->password)) {
            Auth::guard('depts')->login($dept, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('showdept.dashboard'));
        }

        $student = Student::where('Stud_email', $email)->first();

        if ($student) {
            if ((int) $student->is_revoked === 1) {
                return redirect()->back()->withErrors([
                    'account' => 'Your account has been revoked. Please contact administration.',
                ]);
            }

            if (Hash::check($password, $student->password)) {
                Auth::guard('web')->login($student, $remember);
                $request->session()->regenerate();

                return redirect()->intended(route('student.dashboard'));
            }
        }

        return back()->withErrors(['credentials' => 'Invalid email or password.']);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('admins')->logout();
        Auth::guard('depts')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
