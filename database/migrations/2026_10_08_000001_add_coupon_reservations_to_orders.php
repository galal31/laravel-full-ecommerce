<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('reserved_uses')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('coupon_usage_status', ['reserved', 'used', 'released'])->nullable();
            $table->string('payment_review_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_usage_status', 'payment_review_reason']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('reserved_uses');
        });
    }
};
