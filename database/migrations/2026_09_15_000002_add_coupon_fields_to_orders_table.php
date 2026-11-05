<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->after('user_id')
                ->constrained('coupons')
                ->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->unsignedTinyInteger('coupon_discount_percentage')
                ->nullable()
                ->after('coupon_code');
            $table->decimal('coupon_discount_amount', 8, 2)
                ->default(0)
                ->after('coupon_discount_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'coupon_code',
                'coupon_discount_percentage',
                'coupon_discount_amount',
            ]);
        });
    }
};
