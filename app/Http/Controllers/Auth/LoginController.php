<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Get the intended URL and check if it's appropriate for the user's role
            $user = Auth::user();
            $intendedUrl = $request->session()->get('url.intended');

            // Clear the intended URL to prevent cross-role redirects
            $request->session()->forget('url.intended');

            // Determine the appropriate dashboard based on role
            $dashboardUrl = match ($user->role) {
                'admin' => '/admin/dashboard',
                'lecturer' => '/lecturer/dashboard',
                'student' => '/student/dashboard',
                default => '/dashboard'
            };

            // Only use intended URL if it's appropriate for the user's role
            if ($intendedUrl && $this->isUrlAppropriateForRole($intendedUrl, $user->role)) {
                return redirect($intendedUrl);
            }

            return redirect($dashboardUrl);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Handle logout even if session has expired
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            // Clear any intended URL from session
            $request->session()->forget('url.intended');
        } catch (\Exception $e) {
            // If session operations fail, just clear auth
            Auth::logout();
        }

        return redirect('/login')->with('message', 'You have been logged out successfully.');
    }

    /**
     * Check if the intended URL is appropriate for the user's role
     */
    private function isUrlAppropriateForRole($url, $role)
    {
        // Remove domain and get path
        $path = parse_url($url, PHP_URL_PATH);

        // Check if the path matches the user's role
        switch ($role) {
            case 'admin':
                return str_starts_with($path, '/admin/');
            case 'lecturer':
                return str_starts_with($path, '/lecturer/');
            case 'student':
                return str_starts_with($path, '/student/');
            default:
                return str_starts_with($path, '/dashboard');
        }
    }
}
