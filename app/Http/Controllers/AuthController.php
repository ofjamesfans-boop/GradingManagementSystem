<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(string $portal = 'admin'): View
    {
        $portals = [
            'admin' => ['title' => 'Admin / Registrar Login', 'roles' => ['administrator', 'registrar'], 'icon' => 'shield-check', 'route' => 'admin.login.store'],
            'faculty' => ['title' => 'Faculty Login', 'roles' => ['instructor'], 'icon' => 'presentation', 'route' => 'faculty.login.store'],
            'student' => ['title' => 'Student Login', 'roles' => ['student'], 'icon' => 'graduation-cap', 'route' => 'student.login.store'],
        ];
        abort_unless(isset($portals[$portal]), 404);

        return view('auth.login', ['portal' => $portal] + $portals[$portal]);
    }

    public function login(Request $request, string $portal): RedirectResponse
    {
        $allowedRoles = [
            'admin' => ['administrator', 'registrar'],
            'faculty' => ['instructor'],
            'student' => ['student'],
        ];
        abort_unless(isset($allowedRoles[$portal]), 404);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials + ['status' => 'active'])) {
            if (! in_array($request->user()->role, $allowedRoles[$portal], true)) {
                Auth::logout();
                return back()->withErrors(['email' => "This account cannot use the {$portal} portal."])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['password' => 'Invalid login details.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $loginRoute = match ($request->user()?->role) {
            'instructor' => 'faculty.login',
            'student' => 'student.login',
            default => 'login',
        };
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($loginRoute);
    }
}
