<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruction_item_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruction_item_id')->constrained('instruction_items')->cascadeOnDelete();
            $table->bigInteger('old_price');
            $table->bigInteger('new_price');
            $table->string('currency', 3)->default('PHP');
            $table->string('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruction_item_price_histories');
    }
};