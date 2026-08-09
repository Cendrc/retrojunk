<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Password - Retro Junk')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan untuk mereset password akun Retro Junk kamu.')
                ->action('Reset Password', $url)
                ->line('Link ini akan kedaluwarsa dalam 60 menit.')
                ->line('Kalau kamu tidak meminta reset password ini, abaikan saja email ini — password kamu tidak akan berubah.');
        });
    }
}
