<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = $request->user();

                    // Default targets by role
                    $defaultTarget = $user && $user->role === 'admin' ? route('admin.dashboard') : route('dashboard');

                    // Read and remove intended URL from session to control redirect behavior
                    $intended = $request->session()->pull('url.intended');

                    if ($user && $user->role === 'admin') {
                        // For admins, only honor intended URL if it points to an admin path
                        if ($intended) {
                            $path = parse_url($intended, PHP_URL_PATH) ?? '';
                            if (\Illuminate\Support\Str::startsWith($path, '/admin')) {
                                return redirect()->to($intended);
                            }
                        }
                        return redirect()->to($defaultTarget);
                    }

                    // For non-admin users, always send to user dashboard
                    return redirect()->to($defaultTarget);
                }
            };
        });

        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    $user = $request->user();
                    
                    // For new registrations, always redirect to dashboard based on role
                    if ($user && $user->role === 'admin') {
                        return redirect()->route('admin.dashboard');
                    }
                    
                    return redirect()->route('dashboard');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
