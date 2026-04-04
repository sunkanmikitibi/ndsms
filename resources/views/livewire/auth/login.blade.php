<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status
            class="text-center rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-emerald-300"
            :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <div class="flex flex-col gap-2">
                <label for="email" class="block text-sm font-medium text-neutral-300">
                    {{ __('Email address') }}
                </label>
                <div class="relative">
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="email@example.com"
                        class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                </div>
                @error('email')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-neutral-300">
                        {{ __('Password') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs font-medium text-emerald-500 hover:text-emerald-400 transition-colors"
                            wire:navigate>
                            {{ __('Forgot?') }}
                        </a>
                    @endif
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="Enter your password"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('password')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-3">
                <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 bg-neutral-800 border border-neutral-700 rounded checked:bg-emerald-600 checked:border-emerald-600 focus:ring-2 focus:ring-emerald-600 cursor-pointer transition-colors" />
                <label for="remember" class="text-sm text-neutral-400 cursor-pointer">
                    {{ __('Remember me') }}
                </label>
            </div>

            <button type="submit" data-test="login-button"
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold rounded-lg hover:from-emerald-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-neutral-900 transition-all duration-200 shadow-lg hover:shadow-emerald-500/25">
                {{ __('Log in') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm text-neutral-400">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}"
                    class="inline font-semibold text-emerald-500 hover:text-emerald-400 transition-colors"
                    wire:navigate>
                    {{ __('Sign up') }}
                </a>
            </div>
        @endif
    </div>
</x-layouts::auth>
