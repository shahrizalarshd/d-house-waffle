@extends('layouts.app')

@section('title', 'Platform Settings')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Platform Settings</h1>
            <a href="{{ route('super.dashboard') }}" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        {{-- Flash messages handled by toast notifications --}}

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm"><i class="fas fa-times mr-2"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Billplz Settings -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-credit-card mr-2"></i>Billplz Payment Gateway
                </h2>
            </div>

            <form method="POST" action="{{ route('super.settings.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <!-- Enable Billplz -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="billplz_enabled" value="1"
                            {{ $settings->get('billplz_enabled')?->getCastedValue() ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-3 text-gray-700 font-semibold">
                            Enable Billplz Payment Gateway
                        </span>
                    </label>
                    <p class="text-xs text-gray-600 mt-1 ml-8">
                        Allow buyers to pay using FPX, Cards, and E-wallets through Billplz
                    </p>
                </div>

                <!-- Sandbox Mode -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="billplz_sandbox_mode" value="1"
                            {{ $settings->get('billplz_sandbox_mode')?->getCastedValue() ? 'checked' : '' }}
                            class="w-5 h-5 text-yellow-600 rounded focus:ring-yellow-500">
                        <span class="ml-3 text-gray-700 font-semibold">
                            Sandbox/Testing Mode
                        </span>
                    </label>
                    <p class="text-xs text-gray-600 mt-1 ml-8">
                        Use Billplz sandbox for testing (no real money). Uncheck for production.
                    </p>
                </div>

                <hr class="my-6">

                <!-- API Key -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="billplz_api_key">
                        <i class="fas fa-key mr-2"></i>API Secret Key
                    </label>
                    <input type="password" name="billplz_api_key" id="billplz_api_key"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 font-mono text-sm"
                        value="{{ $settings->get('billplz_api_key')?->value }}" placeholder="abc123-def456-ghi789">
                    <p class="text-xs text-gray-600 mt-1">
                        Get this from Billplz Dashboard → Settings → API Keys
                    </p>
                </div>

                <!-- Collection ID -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="billplz_collection_id">
                        <i class="fas fa-folder mr-2"></i>Collection ID
                    </label>
                    <input type="text" name="billplz_collection_id" id="billplz_collection_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 font-mono text-sm"
                        value="{{ $settings->get('billplz_collection_id')?->value }}" placeholder="abc_xyz123">
                    <p class="text-xs text-gray-600 mt-1">
                        Get this from Billplz Dashboard → Collections
                    </p>
                </div>

                <!-- X Signature -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="billplz_x_signature">
                        <i class="fas fa-shield-alt mr-2"></i>X Signature Key
                    </label>
                    <input type="password" name="billplz_x_signature" id="billplz_x_signature"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 font-mono text-sm"
                        value="{{ $settings->get('billplz_x_signature')?->value }}" placeholder="S-xxxxxxxxxxxxxxxx">
                    <p class="text-xs text-gray-600 mt-1">
                        Used for webhook signature verification. Get from Billplz Dashboard.
                    </p>
                </div>

                <hr class="my-6">

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>How to Get Billplz Credentials
                    </h3>
                    <ol class="text-sm text-blue-800 space-y-1 ml-4 list-decimal">
                        <li>Sign up at <a href="https://www.billplz.com/join" target="_blank"
                                class="underline">billplz.com/join</a></li>
                        <li>Complete verification (MyKad/Business Reg)</li>
                        <li>Add your bank account</li>
                        <li>Create a Collection</li>
                        <li>Get API Secret Key from Settings → API Keys</li>
                        <li>Copy Collection ID from Collections page</li>
                        <li>Set webhook URL: <code
                                class="bg-blue-100 px-2 py-1 rounded">{{ route('webhook.billplz') }}</code></li>
                    </ol>
                </div>

                <!-- Webhook URL -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-yellow-800 mb-2">
                        <i class="fas fa-link mr-2"></i>Webhook URL (Set in Billplz)
                    </h3>
                    <div class="flex items-center space-x-2">
                        <input type="text" readonly value="{{ route('webhook.billplz') }}"
                            class="flex-1 px-3 py-2 bg-white border border-yellow-300 rounded font-mono text-sm"
                            id="webhook-url">
                        <button type="button" onclick="copyWebhookUrl()"
                            class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    <p class="text-xs text-yellow-800 mt-2">
                        Add this URL in Billplz Dashboard → Settings → Webhook URL
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                    <a href="{{ route('super.settings.test-billplz') }}"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition text-center">
                        <i class="fas fa-vial mr-2"></i>Test Connection
                    </a>
                </div>
            </form>
        </div>

        <!-- ToyyibPay Settings (Coming Soon) -->
        <div class="bg-white rounded-lg shadow">
            <div class="bg-gray-600 text-white px-6 py-4 rounded-t-lg">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-credit-card mr-2"></i>ToyyibPay Payment Gateway
                </h2>
            </div>

            <div class="p-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <i class="fas fa-tools text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600 font-semibold">Coming Soon</p>
                    <p class="text-sm text-gray-500 mt-2">
                        ToyyibPay integration will be available in future updates
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyWebhookUrl() {
                const input = document.getElementById('webhook-url');
                input.select();
                document.execCommand('copy');
                alert('Webhook URL copied to clipboard!');
            }
        </script>
    @endpush
@endsection
