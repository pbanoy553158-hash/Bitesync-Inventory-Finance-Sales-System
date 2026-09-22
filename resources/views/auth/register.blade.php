<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiteSync - Sign Up</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#1C130D] min-h-screen flex items-center justify-center font-sans antialiased">

    <div class="w-full max-w-6xl mx-auto flex flex-col md:flex-row min-h-[650px] shadow-2xl rounded-2xl overflow-hidden my-6">
        
        <!-- Left Banner Column -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-[#3B2012] via-[#28150B] to-[#1A0C06] p-10 md:p-14 text-white flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center space-x-3 mb-12">
                    <div class="w-12 h-12 bg-[#D87031] rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-md">
                        B
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight leading-none">BiteSync</h1>
                        <p class="text-xs text-gray-400 font-medium tracking-wide mt-1">INVENTORY MANAGEMENT SYSTEM</p>
                    </div>
                </div>

                <!-- Hero Heading -->
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight mb-4">
                    Manage smarter.<br>
                    <span class="text-[#E07A3B]">Serve better.</span>
                </h2>

                <p class="text-gray-300 text-sm md:text-base leading-relaxed mb-8">
                    A centralized workspace for managing inventory, procurement, sales records, expenses, and business reports for The Crazy Bite Co.
                </p>

                <!-- Features Checklist -->
                <ul class="space-y-4 text-sm font-medium text-gray-200">
                    <li class="flex items-center space-x-3">
                        <span class="w-6 h-6 bg-[#2B1B13] border border-[#523120] rounded-md flex items-center justify-center text-[#E07A3B] text-xs font-bold">✓</span>
                        <span>Centralized inventory management</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <span class="w-6 h-6 bg-[#2B1B13] border border-[#523120] rounded-md flex items-center justify-center text-[#E07A3B] text-xs font-bold">✓</span>
                        <span>Procurement and stock monitoring</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <span class="w-6 h-6 bg-[#2B1B13] border border-[#523120] rounded-md flex items-center justify-center text-[#E07A3B] text-xs font-bold">✓</span>
                        <span>Financial and operational records</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Form Column -->
        <div class="w-full md:w-1/2 bg-[#FAF8F5] p-10 md:p-14 flex flex-col justify-between">
            <div class="max-w-md mx-auto w-full">
                <!-- Form Header -->
                <p class="text-xs font-bold uppercase tracking-widest text-[#B85C28] mb-1">Get Started</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">Create your account</h3>
                <p class="text-sm text-gray-500 mb-6">Enter your details below to register your workspace.</p>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Full Name</label>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Enter your full name" 
                                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#B85C28] focus:border-transparent transition">
                            <span class="absolute left-3 top-3.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                placeholder="Enter your email address" 
                                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#B85C28] focus:border-transparent transition">
                            <span class="absolute left-3 top-3.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Password</label>
                        <input id="password" type="password" name="password" required
                            placeholder="Create a password" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#B85C28] focus:border-transparent transition">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="Confirm your password" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#B85C28] focus:border-transparent transition">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#B85C28] hover:bg-[#A04E20] text-white font-medium py-3 px-4 rounded-xl shadow-md transition duration-200 mt-2">
                        Sign Up
                    </button>
                </form>

                <!-- Back to Sign In Link -->
                <div class="text-center mt-4 text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#B85C28] font-semibold hover:underline">
                        Sign In
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-400 mt-6">
                <p>BiteSync · The Crazy Bite Co.</p>
                <p class="mt-1 flex items-center justify-center gap-1 text-gray-500">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Secure system registration
                </p>
            </div>
        </div>

    </div>

</body>
</html>