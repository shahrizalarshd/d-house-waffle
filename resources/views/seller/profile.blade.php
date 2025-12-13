@extends('layouts.app')

@section('title', 'Seller Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-user-circle mr-2"></i>Seller Profile
    </h1>

    {{-- Flash messages handled by toast notifications --}}

    <!-- Basic Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">
            <i class="fas fa-info-circle mr-2 text-amber-600"></i>Basic Information
        </h2>

        <form method="POST" action="{{ route('owner.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4 mb-4">
                <!-- Name -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-user mr-1"></i>Name
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $seller->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                </div>

                <!-- Email (Read only) -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-envelope mr-1"></i>Email
                    </label>
                    <input type="email" 
                           value="{{ $seller->email }}"
                           disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-lock mr-1"></i>Email cannot be changed
                    </p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-phone mr-1"></i>Phone Number
                    </label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone', $seller->phone) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                           placeholder="012-3456789">
                </div>

                <!-- Unit & Block -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-door-open mr-1"></i>Unit No
                        </label>
                        <input type="text" 
                               name="unit_no" 
                               value="{{ old('unit_no', $seller->unit_no) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                               placeholder="G-01">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-building mr-1"></i>Block
                        </label>
                        <input type="text" 
                               name="block" 
                               value="{{ old('block', $seller->block) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                               placeholder="Ground">
                    </div>
                </div>

                <!-- Apartment -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-home mr-1"></i>Business Location / Apartment
                    </label>
                    <select name="apartment_id" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        @foreach(\App\Models\Apartment::where('status', 'active')->get() as $apt)
                        <option value="{{ $apt->id }}" {{ $seller->apartment_id == $apt->id ? 'selected' : '' }}>
                            {{ $apt->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Select the location where you operate your business
                    </p>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg hover:shadow-lg transition font-bold">
                <i class="fas fa-save mr-2"></i>Update Profile
            </button>
        </form>
    </div>

    <!-- QR Payment Setup -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">
            <i class="fas fa-qrcode mr-2 text-purple-600"></i>QR Payment Setup
        </h2>

        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg mb-6">
            <p class="text-sm text-purple-800 mb-2">
                <i class="fas fa-lightbulb mr-1"></i>
                <strong>Enable QR Payment for Your Products!</strong>
            </p>
            <p class="text-xs text-purple-700">
                Upload your DuitNow QR code or any e-wallet QR code. Buyers will scan and pay directly to you!
            </p>
        </div>

        <form method="POST" action="{{ route('owner.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Current QR Code -->
            @if($seller->hasQRCode())
            <div class="mb-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Current QR Code:</p>
                <div class="inline-block bg-gray-100 p-4 rounded-lg">
                    <img src="{{ $seller->getQRCodeUrl() }}" 
                         alt="Current QR Code" 
                         class="w-48 h-48 object-contain">
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Upload new image to replace
                </p>
            </div>
            @endif

            <!-- Upload New QR -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-upload mr-1"></i>
                    QR Code Image @if(!$seller->hasQRCode())*@endif
                </label>
                <input type="file" 
                       name="qr_code_image" 
                       accept="image/*"
                       @if(!$seller->hasQRCode()) required @endif
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                <p class="text-xs text-gray-600 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Take screenshot of your DuitNow QR, TNG QR, or any bank QR code
                </p>
            </div>

            <!-- QR Type -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-tag mr-1"></i>
                    QR Type
                </label>
                <select name="qr_code_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    <option value="">Select type (optional)</option>
                    <option value="DuitNow" {{ $seller->qr_code_type === 'DuitNow' ? 'selected' : '' }}>DuitNow QR</option>
                    <option value="TNG" {{ $seller->qr_code_type === 'TNG' ? 'selected' : '' }}>Touch 'n Go eWallet</option>
                    <option value="Boost" {{ $seller->qr_code_type === 'Boost' ? 'selected' : '' }}>Boost</option>
                    <option value="GrabPay" {{ $seller->qr_code_type === 'GrabPay' ? 'selected' : '' }}>GrabPay</option>
                    <option value="MAE" {{ $seller->qr_code_type === 'MAE' ? 'selected' : '' }}>Maybank MAE</option>
                    <option value="Other" {{ $seller->qr_code_type === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <p class="text-xs text-gray-600 mt-1">
                    Help buyers know which app to use
                </p>
            </div>

            <!-- Instructions -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-comment-dots mr-1"></i>
                    Payment Instructions (Optional)
                </label>
                <textarea name="qr_code_instructions" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                          placeholder="E.g., Scan with any banking app, Send exact amount, WhatsApp me after payment, etc.">{{ $seller->qr_code_instructions }}</textarea>
                <p class="text-xs text-gray-600 mt-1">
                    Additional notes for buyers
                </p>
            </div>

            <!-- How to Get QR Guide -->
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6">
                <p class="text-sm font-bold text-blue-900 mb-2">
                    <i class="fas fa-question-circle mr-1"></i>
                    How to Get Your QR Code:
                </p>
                <ol class="list-decimal ml-5 text-xs text-blue-800 space-y-1">
                    <li>Open your banking app (Maybank, CIMB, Public Bank, etc.)</li>
                    <li>Go to "DuitNow QR" or "Receive Money"</li>
                    <li>Find your personal QR code</li>
                    <li>Take screenshot</li>
                    <li>Upload here!</li>
                </ol>
                <p class="text-xs text-blue-700 mt-2 italic">
                    💡 DuitNow QR works with ALL Malaysian banking apps!
                </p>
            </div>

            <button type="submit" 
                    class="w-full waffle-gradient text-white py-3 rounded-lg hover:shadow-lg transition font-bold">
                <i class="fas fa-save mr-2"></i>
                {{ $seller->hasQRCode() ? 'Update QR Settings' : 'Save QR Settings' }}
            </button>
        </form>

        <!-- QR Status -->
        <div class="mt-6 p-4 rounded-lg {{ $seller->hasQRCode() ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
            @if($seller->hasQRCode())
            <p class="text-green-800 font-semibold">
                <i class="fas fa-check-circle mr-2"></i>
                QR Payment: ENABLED
            </p>
            <p class="text-xs text-green-700 mt-1">
                Buyers can now pay via QR code for your products!
            </p>
            @else
            <p class="text-yellow-800 font-semibold">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                QR Payment: NOT SETUP
            </p>
            <p class="text-xs text-yellow-700 mt-1">
                Upload your QR code to accept QR payments
            </p>
            @endif
        </div>
    </div>
</div>
@endsection

