<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderAndUserEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_form_stores_the_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'username' => 'boss',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'storelead',
                'email' => 'Joji.Store@gmail.com',
                'password' => 'password1',
                'status' => 'active',
                'role' => 'superadmin',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'storelead',
            'email' => 'joji.store@gmail.com',
        ]);
    }

    public function test_banner_upload_accepts_an_image_over_one_megabyte(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'username' => 'bannerboss',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'title' => 'Festival',
                'image' => UploadedFile::fake()->image('banner.png')->size(2000),
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('banners', ['title' => 'Festival']);
    }

    public function test_shipped_update_stores_the_tracking_company(): void
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'username' => 'shipboss',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'razorpay_order_id' => 'order_track_1',
            'customer_name' => 'Ada',
            'email' => 'ada@example.com',
            'phone' => '9876543210',
            'amount' => 150000,
            'currency' => 'INR',
            'status' => 'paid',
            'items' => [],
        ]);

        $this->actingAs($admin)
            ->from(route('admin.orders.show', $order))
            ->post(route('admin.orders.whatsapp', $order), [
                'fulfillment_status' => 'shipped',
                'tracking_company' => 'Delhivery',
                'tracking_number' => 'TRK123',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();

        $this->assertSame('shipped', $order->fulfillment_status);
        $this->assertSame('Delhivery', $order->tracking_company);
        $this->assertSame('TRK123', $order->tracking_number);
    }

    public function test_paid_order_emails_the_store_and_keeps_the_customer_email(): void
    {
        Mail::fake();
        config([
            'services.razorpay.key' => '',
            'services.razorpay.secret' => 'testsecret',
            'services.orders.notify_email' => 'jojiav@gmail.com,vineeshcv88@gmail.com',
        ]);

        Order::query()->create([
            'razorpay_order_id' => 'order_paid_1',
            'customer_name' => 'Meera Nair',
            'email' => 'meera@example.com',
            'phone' => '9876543210',
            'amount' => 250000,
            'currency' => 'INR',
            'status' => 'pending',
            'items' => [['name' => 'Silk saree', 'qty' => 1, 'unit_paise' => 250000]],
        ]);

        $signature = hash_hmac('sha256', 'order_paid_1|pay_test_1', 'testsecret');

        $this->postJson(route('checkout.razorpay.verify'), [
            'razorpay_order_id' => 'order_paid_1',
            'razorpay_payment_id' => 'pay_test_1',
            'razorpay_signature' => $signature,
        ])->assertOk();

        $this->assertDatabaseHas('orders', [
            'razorpay_order_id' => 'order_paid_1',
            'status' => 'paid',
            'email' => 'meera@example.com',
        ]);

        Mail::assertSent(OrderPlacedMail::class, function (OrderPlacedMail $mail): bool {
            return $mail->hasTo('jojiav@gmail.com')
                && $mail->hasTo('vineeshcv88@gmail.com')
                && $mail->orders->first()?->email === 'meera@example.com';
        });
    }
}
