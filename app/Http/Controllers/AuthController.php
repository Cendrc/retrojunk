<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect admin ke dashboard, customer ke home
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegister()
    {
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        Auth::login($user);
        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function account()
    {
        return view('pages.account');
    }

    public function orders()
    {
        $orders = Order::with('items.product')
            ->where('email', auth()->user()->email)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->get();
        return view('pages.orders', compact('orders'));
    }

    public function cancelOrder($id)
    {
        $order = Order::with('items')
            ->where('id', $id)
            ->where(function ($q) {
                $q->where('email', auth()->user()->email)
                  ->orWhere('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($order->order_status !== 'pending') {
            return back()->with('error', 'Pesanan ini sudah diproses dan tidak bisa dibatalkan sendiri. Silakan hubungi kami.');
        }

        $order->cancelAndRestoreStock();

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function showForgotPassword()
    {
        return view('pages.auth.forgot-password');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('password.forgot')->with('success', 'Link reset password sudah dikirim ke email kamu. Silakan cek inbox (atau folder spam).');
        }

        return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.'])->withInput();
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('pages.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->password = $password;
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru kamu.');
        }

        return back()->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.'])->withInput();
    }
}
