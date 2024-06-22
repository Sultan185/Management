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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('barcode_number',13);
            $table->string('location',100)->nullable();
            $table->string('responsible',100)->nullable();
            $table->text('purchase_information')->nullable();
            $table->date('purchase_date');
            $table->text('additional_information')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories','id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
