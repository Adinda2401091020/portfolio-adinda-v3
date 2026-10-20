<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register - {{ config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-[#FDFDFC] px-6 py-12 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <main class="mx-auto w-full max-w-md">
            <a href="{{ url('/') }}" class="text-sm underline underline-offset-4">Home</a>
            <h1 class="mt-6 text-2xl font-semibold">Create an account</h1>
            <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">Register to get started.</p>

            @if (session('status'))
                <p role="status" class="mt-4 rounded border border-green-600 p-3 text-sm">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                        class="w-full rounded border border-[#19140035] bg-transparent px-3 py-2 dark:border-[#3E3E3A]">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full rounded border border-[#19140035] bg-transparent px-3 py-2 dark:border-[#3E3E3A]">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium">Phone (optional)</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"
                        class="w-full rounded border border-[#19140035] bg-transparent px-3 py-2 dark:border-[#3E3E3A]">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="w-full rounded border border-[#19140035] bg-transparent px-3 py-2 dark:border-[#3E3E3A]">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="w-full rounded border border-[#19140035] bg-transparent px-3 py-2 dark:border-[#3E3E3A]">
                </div>

                <button type="submit"
                    class="w-full rounded bg-[#1b1b18] px-4 py-2 font-medium text-white hover:bg-[#3a3a35] dark:bg-[#EDEDEC] dark:text-[#1b1b18]">
                    Register
                </button>
            </form>
        </main>
    </body>
</html>
