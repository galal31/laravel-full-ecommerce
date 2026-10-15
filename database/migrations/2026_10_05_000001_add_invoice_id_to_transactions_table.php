<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_id')->nullable()->unique();
            $table->string('transaction_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // نحتفظ بإمكانية null لأن الفواتير غير المدفوعة ليس لها رقم معاملة.
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
};
