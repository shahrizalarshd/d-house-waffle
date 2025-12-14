<?php

namespace Tests\Unit\Models;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_apartment(): void
    {
        $apartment = Apartment::factory()->create();
        $order = Order::factory()->create(['apartment_id' => $apartment->id]);

        $this->assertInstanceOf(Apartment::class, $order->apartment);
        $this->assertEquals($apartment->id, $order->apartment->id);
    }

    public function test_order_belongs_to_buyer(): void
    {
        $buyer = User::factory()->customer()->create();
        $order = Order::factory()->create(['buyer_id' => $buyer->id]);

        $this->assertInstanceOf(User::class, $order->buyer);
        $this->assertEquals($buyer->id, $order->buyer->id);
    }

    public function test_order_belongs_to_seller(): void
    {
        $seller = User::factory()->owner()->create();
        $order = Order::factory()->create(['seller_id' => $seller->id]);

        $this->assertInstanceOf(User::class, $order->seller);
        $this->assertEquals($seller->id, $order->seller->id);
    }

    public function test_order_has_many_items(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertTrue($order->items->contains($item));
    }

    public function test_is_pending_returns_correct_value(): void
    {
        $pendingOrder = Order::factory()->create(['status' => 'pending']);
        $preparingOrder = Order::factory()->create(['status' => 'preparing']);

        $this->assertTrue($pendingOrder->isPending());
        $this->assertFalse($preparingOrder->isPending());
    }

    public function test_is_preparing_returns_correct_value(): void
    {
        $order = Order::factory()->preparing()->create();

        $this->assertTrue($order->isPreparing());
        $this->assertFalse($order->isPending());
    }

    public function test_is_ready_returns_correct_value(): void
    {
        $order = Order::factory()->ready()->create();

        $this->assertTrue($order->isReady());
        $this->assertFalse($order->isPreparing());
    }

    public function test_is_completed_returns_correct_value(): void
    {
        $order = Order::factory()->completed()->create();

        $this->assertTrue($order->isCompleted());
        $this->assertTrue($order->isPaid());
    }

    public function test_is_cancelled_returns_correct_value(): void
    {
        $order = Order::factory()->cancelled()->create();

        $this->assertTrue($order->isCancelled());
    }

    public function test_is_paid_returns_correct_value(): void
    {
        $unpaidOrder = Order::factory()->create(['payment_status' => 'pending']);
        $paidOrder = Order::factory()->paid()->create();

        $this->assertFalse($unpaidOrder->isPaid());
        $this->assertTrue($paidOrder->isPaid());
    }

    public function test_is_cash_payment_returns_correct_value(): void
    {
        $cashOrder = Order::factory()->create(['payment_method' => 'cash']);
        $onlineOrder = Order::factory()->onlinePayment()->create();

        $this->assertTrue($cashOrder->isCashPayment());
        $this->assertFalse($onlineOrder->isCashPayment());
    }

    public function test_is_online_payment_returns_correct_value(): void
    {
        $onlineOrder = Order::factory()->onlinePayment()->create();
        $cashOrder = Order::factory()->create(['payment_method' => 'cash']);

        $this->assertTrue($onlineOrder->isOnlinePayment());
        $this->assertFalse($cashOrder->isOnlinePayment());
    }

    public function test_is_qr_payment_returns_correct_value(): void
    {
        $qrOrder = Order::factory()->qrPayment()->create();
        $cashOrder = Order::factory()->create(['payment_method' => 'cash']);

        $this->assertTrue($qrOrder->isQRPayment());
        $this->assertFalse($cashOrder->isQRPayment());
    }

    public function test_has_payment_proof_returns_correct_value(): void
    {
        $orderWithProof = Order::factory()->create(['payment_proof' => 'proofs/test.png']);
        $orderWithoutProof = Order::factory()->create(['payment_proof' => null]);

        $this->assertTrue($orderWithProof->hasPaymentProof());
        $this->assertFalse($orderWithoutProof->hasPaymentProof());
    }

    public function test_amounts_are_cast_to_decimal(): void
    {
        $order = Order::factory()->create([
            'total_amount' => 100.50,
            'platform_fee' => 5.03,
            'seller_amount' => 95.47,
        ]);

        $this->assertEquals('100.50', $order->total_amount);
        $this->assertEquals('5.03', $order->platform_fee);
        $this->assertEquals('95.47', $order->seller_amount);
    }
}
