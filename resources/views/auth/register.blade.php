<x-guest-layout>
    <!-- Logo/Company Name -->
    <div class="flex items-center mb-8">
        <div class="w-8 h-8 rounded flex items-center justify-center">
            <img src="{{ asset('Image/logo_polindra.png') }}" alt="LogoPolindra">
        </div>
        <span class="ml-2 text-xl font-semibold dark:text-white">Polindra ImagePlag</span>
    </div>

    <!-- Register Title -->
    <h2 class="text-2xl font-bold mb-2 dark:text-white">Create your account</h2>
    <p class="text-gray-600 mb-8 dark:text-white">Enter your details to register</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="relative">
                <x-text-input id="name" 
                    class="block mt-1 w-full pl-3 pr-10" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Enter your name" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email_username" :value="__('Email')" />
            
            {{-- Grup Input --}}
            <div class="flex rounded-lg shadow-sm mt-1">
                <input type="text" id="email_username" class="py-3 px-3 block w-full border-gray-300 shadow-sm rounded-s-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Name Email" required autofocus>
                
                <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                    <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                </div>
            </div>
            
            <input type="hidden" name="email" id="full_email" value="{{ old('email') }}">

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password-register" 
                    class="block mt-1 w-full pl-3 pr-10"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password"
                    placeholder="Create a password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password-register')">
                    <svg class="h-5 w-5 text-gray-400" fill="none" id="eye-icon-password-register" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="password-confirmation"
                    class="block mt-1 w-full pl-3 pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm your password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password-confirmation')">
                    <svg class="h-5 w-5 text-gray-400" fill="none" id="eye-icon-password-confirmation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <x-primary-button class="w-full justify-center mt-6 bg-black hover:bg-gray-800">
            {{ __('Register') }}
        </x-primary-button>

        <!-- Login Link -->
        <p class="mt-6 text-center text-sm text-gray-600 dark:text-white">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Login here
            </a>
        </p>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById('eye-icon-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                `;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const emailUsernameInput = document.getElementById('email_username');
            const fullEmailInput = document.getElementById('full_email');

            function updateFullEmail() {
                if (emailUsernameInput && fullEmailInput) {
                    let username = emailUsernameInput.value;

                    if (username.includes('@gmail.com')) {
                        fullEmailInput.value = username;
                    } else {
                        fullEmailInput.value = username + '@gmail.com';
                    }
                }
            }

            if(emailUsernameInput) {
                emailUsernameInput.addEventListener('input', updateFullEmail);
                updateFullEmail(); 
            }
        });
    </script>
</x-guest-layout>
