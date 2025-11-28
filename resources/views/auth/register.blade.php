<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Register - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-red-50:oklch(.971 .013 17.38);--color-red-100:oklch(.936 .032 17.717);--color-red-200:oklch(.885 .062 18.334);--color-red-300:oklch(.808 .114 19.571);--color-red-400:oklch(.704 .191 22.216);--color-red-500:oklch(.637 .237 25.331);--color-red-600:oklch(.577 .245 27.325);--color-red-700:oklch(.505 .213 27.518);--color-red-800:oklch(.444 .177 26.899);--color-red-900:oklch(.396 .141 25.723);--color-red-950:oklch(.258 .092 26.042);--color-orange-50:oklch(.98 .016 73.684);--color-orange-100:oklch(.954 .038 75.164);--color-orange-200:oklch(.901 .076 70.697);--color-orange-300:oklch(.837 .128 66.29);--color-orange-400:oklch(.75 .183 55.934);--color-orange-500:oklch(.705 .213 47.604);--color-orange-600:oklch(.646 .222 41.116);--color-orange-700:oklch(.553 .195 38.402);--color-orange-800:oklch(.47 .157 37.304);--color-orange-900:oklch(.408 .123 38.172);--color-orange-950:oklch(.266 .079 36.259);--color-amber-50:oklch(.987 .022 95.277);--color-amber-100:oklch(.962 .059 95.617);--color-amber-200:oklch(.924 .12 95.746);--color-amber-300:oklch(.879 .169 91.605);--color-amber-400:oklch(.828 .189 84.429);--color-amber-500:oklch(.769 .188 70.08);--color-amber-600:oklch(.666 .179 58.318);--color-amber-700:oklch(.555 .163 48.998);--color-amber-800:oklch(.473 .137 46.201);--color-amber-900:oklch(.414 .112 45.904);--color-amber-950:oklch(.279 .077 45.635);--color-yellow-50:oklch(.987 .026 102.212);--color-yellow-100:oklch(.973 .071 103.193);--color-yellow-200:oklch(.945 .129 101.54);--color-yellow-300:oklch(.905 .182 98.111);--color-yellow-400:oklch(.852 .199 91.936);--...[26398 bytes truncated]
            </style>
        @endif
        
        <style>
            body {
                font-family: 'Instrument Sans', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #FDFDFC;
                color: #1b1b18;
            }
            
            .container {
                display: flex;
                min-height: 100vh;
            }
            
            .left-panel {
                flex: 1;
                background: linear-gradient(to bottom, #1f58b6, #2a64c6, #1b4fa3);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem;
                position: relative;
                overflow: hidden;
            }
            
            .left-panel::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 560' fill='none'%3E%3Cpath d='M0 120 C 180 60, 360 180, 540 120 C 720 60, 900 180, 1080 120 C 1260 60, 1440 180, 1620 120 L 1620 0 L 0 0 Z' fill='url(%23g1)'/%3E%3Cpath d='M0 300 C 200 240, 380 360, 600 300 C 820 240, 1000 360, 1220 300 C 1440 240, 1620 360, 1800 300 L 1800 0 L 0 0 Z' fill='url(%23g2)' opacity='.7'/%3E%3Cdefs%3E%3ClinearGradient id='g1' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0%25' stop-color='%2358a6ff'/%3E%3Cstop offset='100%25' stop-color='%235ac8fa'/%3E%3C/linearGradient%3E%3ClinearGradient id='g2' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0%25' stop-color='%237cc2ff'/%3E%3Cstop offset='100%25' stop-color='%239bd4ff'/%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E");
                background-size: cover;
                opacity: 0.3;
            }
            
            .logo-container {
                position: relative;
                z-index: 10;
                margin-bottom: 2rem;
            }
            
            .logo {
                height: 100px;
                width: 100px;
                object-fit: contain;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.1);
                padding: 0.5rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .left-content {
                position: relative;
                z-index: 10;
                text-align: center;
                color: white;
                max-width: 400px;
            }
            
            .left-content h1 {
                font-size: 2.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }
            
            .left-content p {
                font-size: 1.25rem;
                opacity: 0.9;
            }
            
            .right-panel {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 2rem;
            }
            
            .form-container {
                max-width: 400px;
                width: 100%;
            }
            
            .form-header {
                margin-bottom: 2rem;
            }
            
            .form-header h2 {
                font-size: 1.75rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            
            .form-header p {
                color: #6b7280;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
            
            label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
                color: #374151;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 0.75rem 1rem;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                font-size: 1rem;
                transition: all 0.2s;
            }
            
            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                outline: none;
                border-color: #1f58b6;
                box-shadow: 0 0 0 3px rgba(31, 88, 182, 0.1);
            }
            
            .checkbox-group {
                display: flex;
                align-items: flex-start;
                margin-bottom: 1.5rem;
            }
            
            .checkbox-group input {
                margin-top: 0.25rem;
                margin-right: 0.5rem;
            }
            
            .checkbox-group label {
                font-size: 0.875rem;
                color: #6b7280;
                margin-bottom: 0;
            }
            
            .checkbox-group a {
                color: #1f58b6;
                text-decoration: none;
            }
            
            .checkbox-group a:hover {
                text-decoration: underline;
            }
            
            .btn-primary {
                width: 100%;
                padding: 0.75rem 1rem;
                background-color: #1f58b6;
                color: white;
                border: none;
                border-radius: 9999px;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s;
                margin-bottom: 1rem;
            }
            
            .btn-primary:hover {
                background-color: #1b4fa3;
            }
            
            .btn-secondary {
                width: 100%;
                padding: 0.75rem 1rem;
                background-color: transparent;
                color: #1f58b6;
                border: 1px solid rgba(31, 88, 182, 0.3);
                border-radius: 9999px;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            
            .btn-secondary:hover {
                background-color: rgba(31, 88, 182, 0.1);
            }
            
            .validation-errors {
                background-color: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 0.5rem;
                padding: 1rem;
                margin-bottom: 1.5rem;
                color: #dc2626;
                font-size: 0.875rem;
            }
            
            @media (max-width: 768px) {
                .container {
                    flex-direction: column;
                }
                
                .left-panel {
                    padding: 2rem 1rem;
                    min-height: 40vh;
                }
                
                .left-content h1 {
                    font-size: 2rem;
                }
                
                .right-panel {
                    padding: 2rem 1rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Left Panel -->
            <div class="left-panel">
                <div class="logo-container">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="logo" />
                </div>
                <div class="left-content">
                    <h1>Create Account</h1>
                    <p>Join Laundry Stream Wash 'n Dry</p>
                </div>
            </div>
            
            <!-- Right Panel -->
            <div class="right-panel">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Get Started</h2>
                        <p>Fill in your details to create your account</p>
                    </div>
                    
                    <x-validation-errors class="validation-errors" />

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                                placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                                placeholder="Enter your email address">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                placeholder="Create a password">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm your password">
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="checkbox-group">
                                <input type="checkbox" name="terms" id="terms" required>
                                <label for="terms">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'">Terms of Service</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'">Privacy Policy</a>',
                                    ]) !!}
                                </label>
                            </div>
                        @endif

                        <button type="submit" class="btn-primary">
                            {{ __('Register') }}
                        </button>
                        
                        <a href="{{ route('login') }}">
                            <button type="button" class="btn-secondary">
                                {{ __('Already registered?') }}
                            </button>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>