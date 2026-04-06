<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to get started')" />

        <!-- Session Status -->
        <x-auth-session-status
            class="text-center rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-emerald-300"
            :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <div class="flex flex-col gap-2">
                <label for="name" class="block text-sm font-medium text-neutral-300">
                    {{ __('Full name') }}
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="John Doe"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('name')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="flex flex-col gap-2">
                <label for="email" class="block text-sm font-medium text-neutral-300">
                    {{ __('Email address') }}
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    autocomplete="email" placeholder="email@example.com"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('email')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone (Optional) -->
            <div class="flex flex-col gap-2">
                <label for="phone" class="block text-sm font-medium text-neutral-300">
                    {{ __('Phone number') }} <span class="text-neutral-500">(Optional)</span>
                </label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                    placeholder="+234 XXX XXX XXXX"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('phone')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Town (Optional) -->
            <div class="flex flex-col gap-2">
                <label for="town" class="block text-sm font-medium text-neutral-300">
                    {{ __('Town/City') }} <span class="text-neutral-500">(Optional)</span>
                </label>
                <input id="town" name="town" type="text" value="{{ old('town') }}"
                    placeholder="Your town or city"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('town')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label for="password" class="block text-sm font-medium text-neutral-300">
                    {{ __('Password') }}
                </label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    placeholder="Create a strong password"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('password')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-300">
                    {{ __('Confirm password') }}
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password" placeholder="Confirm your password"
                    class="w-full px-4 py-3 bg-neutral-800/50 border border-neutral-700 rounded-lg text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all duration-200" />
                @error('password_confirmation')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" data-test="register-user-button"
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold rounded-lg hover:from-emerald-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-neutral-900 transition-all duration-200 shadow-lg hover:shadow-emerald-500/25">
                {{ __('Create account') }}
            </button>
        </form>

        <div class="text-center text-sm text-neutral-400">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}"
                class="inline font-semibold text-emerald-500 hover:text-emerald-400 transition-colors" wire:navigate>
                {{ __('Log in') }}
            </a>
        </div>
    </div>
</x-layouts::auth>
