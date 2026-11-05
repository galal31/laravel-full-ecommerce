<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // موعد قديم افتراضي للطلبات السابقة؛ الطلب الجديد يحفظ مهلته الفعلية عند إنشائه.
            $table->dateTime('payment_expires_at')->default('2000-01-01 00:00:00');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_expires_at');
        });
    }
};
