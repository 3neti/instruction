<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruction_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('index')->unique();
            $table->string('type')->index();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 3)->default('PHP');
            $table->json('meta')->nullable();
            $table->nullableMorphs('revenue_destination');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruction_items');
    }
};
