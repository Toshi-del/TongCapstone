<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        $loginContent = \App\Models\PageContent::getPageContent('login');
        return view('auth.login', compact('loginContent'));
    }

    /**
     * Show the registration form
     */
    public function showRegister(Request $request)
    {
        $isOpdRegistration = $request->has('opd') && $request->opd == '1';
        $isCorporateRegistration = $request->has('corporate') && $request->corporate == '1';
        
        return view('auth.register', compact('isOpdRegistration', 'isCorporateRegistration'));
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Check if user account is pending approval
            $user = Auth::user();
            
            if ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('login')->with('company_pending', true);
            }
            
            if ($user->status === 'rejected') {
                Auth::logout();
                return redirect()->route('login')->with('account_rejected', true);
            }
            
            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isCompany()) {
                return redirect()->route('company.dashboard');
            } elseif ($user->isDoctor()) {
                return redirect()->route('doctor.dashboard');
            } elseif ($user->isNurse()) {
                return redirect()->route('nurse.dashboard');
            } elseif ($user->isRadTech()) {
                return redirect()->route('radtech.dashboard');
            } elseif ($user->isRadiologist()) {
                return redirect()->route('radiologist.dashboard');
            } elseif ($user->isEcgTech()) {
                return redirect()->route('ecgtech.dashboard');
            } elseif ($user->isPlebo()) {
                return redirect()->route('plebo.dashboard');
            } elseif ($user->isPathologist()) {
                return redirect()->route('pathologist.dashboard');
            } elseif ($user->isOpd()) {
                return redirect()->route('opd.dashboard');
            } else {
                return redirect()->route('patient.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'address' => 'required|string|max:500',
            'birthday' => 'required|date',
            'company' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Additional duplicate prevention checks
        $firstName = trim($request->fname);
        $lastName = trim($request->lname);
        $email = trim($request->email);
        $phone = trim($request->phone);

        // Check for existing user with same name and email combination
        $existingUser = User::where('fname', $firstName)
            ->where('lname', $lastName)
            ->where('email', $email)
            ->first();

        if ($existingUser) {
            return back()->withErrors([
                'email' => 'A user with the same name and email already exists. Please use a different email or contact support if this is an error.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Check for existing user with same name and phone combination
        $existingUserByPhone = User::where('fname', $firstName)
            ->where('lname', $lastName)
            ->where('phone', $phone)
            ->first();

        if ($existingUserByPhone) {
            return back()->withErrors([
                'phone' => 'A user with the same name and phone number already exists. Please use a different phone number or contact support if this is an error.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Calculate age
        $birthday = Carbon::parse($request->birthday);
        $age = $birthday->age;

        // Determine role and status based on request parameters
        $role = 'patient'; // Default role
        $status = 'active'; // Default status
        
        if ($request->has('opd') && $request->opd == '1') {
            $role = 'opd';
        } elseif ($request->has('corporate') && $request->corporate == '1') {
            $role = 'patient'; // Set as patient initially
            $status = 'pending'; // Company accounts need approval
        }

        $user = User::create([
            'fname' => $firstName,
            'lname' => $lastName,
            'mname' => $request->mname,
            'email' => $email,
            'phone' => $phone,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'age' => $age,
            'company' => $request->company,
            'role' => $role,
            'status' => $status,
            'password' => Hash::make($request->password),
        ]);

        // Don't auto-login pending company accounts
        if ($status === 'pending') {
            return redirect()->route('login')->with('company_pending', true);
        }

        Auth::login($user);

        // Redirect based on role
        if ($role === 'opd') {
            return redirect()->route('opd.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
