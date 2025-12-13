@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">👤</span>
        <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
    </div>

    <div class="bg-white rounded-lg shadow-xl p-6 mb-6">
        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r mb-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-xl mr-2 mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('buyer.profile.update') }}">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-amber-600">
                    <i class="fas fa-user-circle"></i> Personal Information
                </h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="name">
                        <i class="fas fa-user mr-1 text-amber-600"></i> Full Name
                    </label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        value="{{ old('name', auth()->user()->name) }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="email">
                        <i class="fas fa-envelope mr-1 text-amber-600"></i> Email
                    </label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        value="{{ old('email', auth()->user()->email) }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="phone">
                        <i class="fas fa-phone mr-1 text-amber-600"></i> Phone Number
                    </label>
                    <input type="text" name="phone" id="phone" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        value="{{ old('phone', auth()->user()->phone) }}">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-amber-600">
                    <i class="fas fa-building"></i> Apartment Details
                </h3>
                
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="block">
                                Block
                            </label>
                            <input type="text" name="block" id="block"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                                placeholder="A, B, C..."
                                value="{{ old('block', auth()->user()->block) }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit_no">
                                Unit No
                            </label>
                            <input type="text" name="unit_no" id="unit_no"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                                placeholder="01-01"
                                value="{{ old('unit_no', auth()->user()->unit_no) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-amber-600">
                    <i class="fas fa-lock"></i> Change Password (Optional)
                </h3>
                <p class="text-xs text-gray-600 mb-4">Leave blank if you don't want to change password</p>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="current_password">
                        Current Password
                    </label>
                    <input type="password" name="current_password" id="current_password"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Enter current password">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="password">
                        New Password
                    </label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Enter new password">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="password_confirmation">
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Confirm new password">
                </div>
            </div>

            <button type="submit" 
                class="w-full waffle-gradient text-white py-4 rounded-lg hover:shadow-xl transition font-bold text-lg">
                <i class="fas fa-save mr-2"></i>Update Profile
            </button>
        </form>
    </div>

    <!-- Account Info -->
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-6 border-l-4 border-amber-500">
        <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
            <i class="fas fa-info-circle text-amber-600"></i> Account Information
        </h3>
        <div class="space-y-2 text-sm text-gray-700">
            <p><strong>Account Type:</strong> {{ ucfirst(auth()->user()->role) }}</p>
            <p><strong>Member Since:</strong> {{ auth()->user()->created_at->format('d M Y') }}</p>
            <p><strong>Total Orders:</strong> {{ auth()->user()->orders->count() }}</p>
        </div>
    </div>
</div>
@endsection

