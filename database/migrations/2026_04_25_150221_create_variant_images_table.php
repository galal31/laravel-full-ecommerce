<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variant_images', function (Blueprint $table) {
            $table->id();

            // مسار أو اسم الصورة
            $table->string('file_name');

            // الربط بجدول المتغيرات
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete(); // لو المتغير اتمسح، صوره تتمسح معاه من الداتابيز

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_images');
    }
};
