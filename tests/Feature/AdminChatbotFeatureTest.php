<?php

namespace Tests\Feature;

use App\Models\AdminChatbotLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\AdminChatbotService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminChatbotFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        config()->set('permission.testing', true);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        app('db')->setDefaultConnection('sqlite');

        $this->createSchema();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_chatbot_service_uses_context_for_follow_up_stock_history(): void
    {
        $admin = $this->makeAdminUser();

        Product::query()->create([
            'product_id' => 'P1001',
            'product_name' => 'Gula Aren',
            'product_category' => 'CAT01',
            'product_image' => 'gula.jpg',
            'product_price' => 18000,
            'buy_price' => 10000,
            'product_profit' => 8000,
            'product_quantity' => 12,
            'product_expired' => '2026-12-31',
        ]);

        StockMovement::query()->create([
            'product_id' => 'P1001',
            'transaction_id' => 'PO-1',
            'product_name' => 'Gula Aren',
            'status' => 1,
            'source' => 'purchase',
            'reason' => 'Restock',
            'quantity_before' => 5,
            'quantity_after' => 12,
            'action_by' => 'Admin',
        ]);

        $service = app(AdminChatbotService::class);

        $first = $service->handle('cek stok gula aren', [], $admin->id, 'feature-context');
        $second = $service->handle('riwayatnya gimana', $first['context'], $admin->id, 'feature-context');

        $this->assertTrue($first['success']);
        $this->assertSame('cek_stok_produk', $first['intent']);
        $this->assertSame('P1001', $first['data']['product_id']);
        $this->assertNotNull($first['log_id']);

        $this->assertTrue($second['success']);
        $this->assertSame('riwayat_stock_movement', $second['intent']);
        $this->assertStringContainsString('Gula Aren', $second['message']);
        $this->assertNotNull($second['log_id']);

        $this->assertDatabaseCount('admin_chatbot_logs', 2);
    }

    public function test_chatbot_feedback_is_saved_after_a_successful_query(): void
    {
        $admin = $this->makeAdminUser();

        Product::query()->create([
            'product_id' => 'P2001',
            'product_name' => 'Bubuk Matcha',
            'product_category' => 'CAT01',
            'product_image' => 'matcha.jpg',
            'product_price' => 35000,
            'buy_price' => 20000,
            'product_profit' => 15000,
            'product_quantity' => 8,
            'product_expired' => '2026-12-31',
        ]);

        $service = app(AdminChatbotService::class);
        $response = $service->handle('cek stok bubuk matcha', [], $admin->id, 'feature-feedback');

        $this->assertNotNull($response['log_id']);
        $this->assertTrue($service->submitFeedback((int) $response['log_id'], 'helpful'));

        $this->assertDatabaseHas('admin_chatbot_logs', [
            'id' => $response['log_id'],
            'feedback' => 'helpful',
        ]);
    }

    protected function makeAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    protected function createSchema(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['team_id', 'name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('categories', function ($table) {
            $table->id();
            $table->string('category_id', 6)->unique();
            $table->string('category_name', 50);
            $table->string('added_by', 40);
            $table->timestamps();
        });

        Schema::create('products', function ($table) {
            $table->id();
            $table->string('product_id')->unique();
            $table->string('product_name', 50);
            $table->string('product_category', 6);
            $table->string('product_image', 35);
            $table->integer('product_price');
            $table->decimal('buy_price', 10, 2)->default(0);
            $table->decimal('product_profit', 12, 2)->default(0);
            $table->integer('product_quantity');
            $table->string('product_expired', 10);
            $table->timestamps();
        });

        Schema::create('cashier_shifts', function ($table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('shift_start');
            $table->dateTime('shift_end')->nullable();
            $table->decimal('opening_cash', 10, 2);
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 10)->default('open');
            $table->timestamps();
        });

        Schema::create('sales', function ($table) {
            $table->id();
            $table->string('sale_id', 35)->unique();
            $table->foreignId('shift_id')->nullable()->constrained('cashier_shifts')->nullOnDelete();
            $table->string('cashier_id', 20);
            $table->integer('total');
            $table->string('payment_method', 1);
            $table->integer('pay');
            $table->integer('change');
            $table->timestamps();
        });

        Schema::create('detail_sales', function ($table) {
            $table->id();
            $table->string('sale_id', 30);
            $table->string('cashier_id', 20);
            $table->string('product_id', 5);
            $table->string('product_name', 50);
            $table->decimal('product_profit', 12, 2)->default(0);
            $table->integer('product_price');
            $table->decimal('buy_price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('sub_total');
            $table->timestamps();
        });

        Schema::create('stock_movements', function ($table) {
            $table->id();
            $table->string('product_id', 5)->nullable();
            $table->string('transaction_id', 35)->nullable();
            $table->string('product_name', 50);
            $table->integer('status');
            $table->string('source', 30)->default('product');
            $table->string('reason', 20);
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('action_by', 40);
            $table->timestamps();
        });

        Schema::create('admin_chatbot_logs', function ($table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id', 120)->nullable();
            $table->string('question', 500);
            $table->string('normalized_question', 500)->nullable();
            $table->string('intent', 80);
            $table->text('parameters')->nullable();
            $table->boolean('success')->default(false);
            $table->text('response_summary')->nullable();
            $table->text('response_meta')->nullable();
            $table->text('context_snapshot')->nullable();
            $table->unsignedInteger('latency_ms')->default(0);
            $table->string('feedback', 20)->nullable();
            $table->timestamp('feedback_at')->nullable();
            $table->timestamps();
        });
    }
}
