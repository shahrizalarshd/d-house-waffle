@extends('layouts.app')

@section('title', 'My Loyalty Card')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">🏆</span>
        <h1 class="text-2xl font-bold text-gray-800">My Loyalty Card</h1>
    </div>

    @if(!$loyaltySummary['enabled'])
    <div class="bg-gray-100 rounded-lg p-8 text-center">
        <div class="text-6xl mb-4">🧇</div>
        <p class="text-gray-600">Loyalty program is currently not available.</p>
    </div>
    @else

    <!-- Loyalty Card -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-6 text-white shadow-lg mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold">🧇 D'house Waffle</h3>
                <p class="text-amber-100 text-sm">Loyalty Card</p>
            </div>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                {{ $loyaltySummary['tier_emoji'] }} {{ ucfirst($loyaltySummary['tier']) }}
            </span>
        </div>
        
        <!-- Stamp Progress -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-2">
                <span>{{ $loyaltySummary['stamps'] }} / {{ $loyaltySummary['stamps_required'] }} stamps</span>
                @if($loyaltySummary['stamps'] >= $loyaltySummary['stamps_required'])
                <span>🎉 Complete!</span>
                @endif
            </div>
            <div class="flex gap-2 justify-center">
                @for($i = 0; $i < $loyaltySummary['stamps_required']; $i++)
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg
                    {{ $i < $loyaltySummary['stamps'] ? 'bg-white text-amber-600' : 'bg-white/20' }}">
                    @if($i < $loyaltySummary['stamps'])
                        🧇
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                @endfor
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-white/20 rounded-full h-2 mb-4">
            <div class="bg-white rounded-full h-2 transition-all duration-500" 
                 style="width: {{ $loyaltySummary['progress_percent'] }}%"></div>
        </div>
        
        <!-- Discount Status -->
        @if($loyaltySummary['has_discount'])
        <div class="bg-white/20 rounded-lg p-3 text-center">
            <p class="font-bold text-lg">🎁 You have {{ $loyaltySummary['discount_percent'] }}% OFF!</p>
            <p class="text-sm text-amber-100">
                Valid until {{ $loyaltySummary['discount_expires_at']->format('d M Y') }}
            </p>
            <a href="{{ route('menu') }}" class="inline-block mt-2 bg-white text-amber-600 px-4 py-2 rounded-lg font-semibold hover:bg-amber-50 transition">
                Use It Now!
            </a>
        </div>
        @elseif($loyaltySummary['is_close_to_discount'])
        <p class="text-center text-white">
            <i class="fas fa-fire animate-pulse"></i>
            Just {{ $loyaltySummary['stamps_remaining'] }} more order(s) to unlock {{ $loyaltySummary['discount_percent'] }}% discount!
        </p>
        @else
        <p class="text-center text-amber-100 text-sm">
            {{ $loyaltySummary['stamps_remaining'] }} more orders to unlock {{ $loyaltySummary['discount_percent'] }}% discount!
        </p>
        @endif
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-amber-600">{{ $loyaltySummary['lifetime_orders'] }}</p>
            <p class="text-sm text-gray-500">Lifetime Orders</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-green-600">RM {{ number_format($loyaltySummary['lifetime_spent'], 0) }}</p>
            <p class="text-sm text-gray-500">Total Spent</p>
        </div>
    </div>

    <!-- Tier Info (if enabled) -->
    @if($loyaltySummary['tiers_enabled'])
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-bold text-gray-800 mb-3">Membership Tiers</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg {{ $loyaltySummary['tier'] === 'bronze' ? 'bg-amber-50 border-2 border-amber-500' : 'bg-gray-50' }}">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🥉</span>
                    <div>
                        <p class="font-semibold">Bronze</p>
                        <p class="text-xs text-gray-500">0-{{ $settings->silver_threshold - 1 }} orders</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">Standard</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg {{ $loyaltySummary['tier'] === 'silver' ? 'bg-gray-100 border-2 border-gray-400' : 'bg-gray-50' }}">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🥈</span>
                    <div>
                        <p class="font-semibold">Silver</p>
                        <p class="text-xs text-gray-500">{{ $settings->silver_threshold }}-{{ $settings->gold_threshold - 1 }} orders</p>
                    </div>
                </div>
                <span class="text-sm text-green-600">+{{ $settings->silver_bonus_percent }}% off</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg {{ $loyaltySummary['tier'] === 'gold' ? 'bg-yellow-50 border-2 border-yellow-500' : 'bg-gray-50' }}">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🥇</span>
                    <div>
                        <p class="font-semibold">Gold</p>
                        <p class="text-xs text-gray-500">{{ $settings->gold_threshold }}+ orders</p>
                    </div>
                </div>
                <span class="text-sm text-green-600">+{{ $settings->gold_bonus_percent }}% off</span>
            </div>
        </div>
    </div>
    @endif

    <!-- How It Works -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-bold text-gray-800 mb-3">How It Works</h3>
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <span class="text-amber-600 font-bold">1</span>
                </div>
                <div>
                    <p class="font-semibold">Order Waffles</p>
                    <p class="text-sm text-gray-500">Each completed order earns you 1 stamp</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <span class="text-amber-600 font-bold">2</span>
                </div>
                <div>
                    <p class="font-semibold">Collect {{ $loyaltySummary['stamps_required'] }} Stamps</p>
                    <p class="text-sm text-gray-500">Complete {{ $loyaltySummary['stamps_required'] }} orders to fill your card</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <span class="text-green-600 font-bold">3</span>
                </div>
                <div>
                    <p class="font-semibold">Get {{ $loyaltySummary['discount_percent'] }}% Off!</p>
                    <p class="text-sm text-gray-500">Enjoy discount on your next order</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    @if($recentTransactions->count() > 0)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold text-gray-800 mb-3">Recent Activity</h3>
        <div class="space-y-3">
            @foreach($recentTransactions as $transaction)
            <div class="flex items-center justify-between text-sm border-b pb-2 last:border-0">
                <div class="flex items-center">
                    @switch($transaction->type)
                        @case('stamp_earned')
                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-green-600"></i>
                            </span>
                            @break
                        @case('discount_unlocked')
                            <span class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-gift text-amber-600"></i>
                            </span>
                            @break
                        @case('discount_used')
                            <span class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-tag text-purple-600"></i>
                            </span>
                            @break
                        @case('tier_upgraded')
                            <span class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-arrow-up text-yellow-600"></i>
                            </span>
                            @break
                        @default
                            <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-circle text-gray-400"></i>
                            </span>
                    @endswitch
                    <div>
                        <p class="font-medium">{{ $transaction->description }}</p>
                        <p class="text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if($transaction->stamps_change != 0)
                <span class="{{ $transaction->stamps_change > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                    {{ $transaction->stamps_change > 0 ? '+' : '' }}{{ $transaction->stamps_change }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
@endsection

