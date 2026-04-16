<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('from_type')->nullable();
            $table->unsignedBigInteger('from_id');
            $table->string('to_type')->nullable();
            $table->unsignedBigInteger('to_id');

            $table->enum('status', ['exchange', 'transfer', 'paid', 'refund', 'gift'])->default('transfer');
            $table->enum('status_last', ['exchange', 'transfer', 'paid', 'refund', 'gift'])->nullable();

            $table->unsignedBigInteger('deposit_id');
            $table->unsignedBigInteger('withdraw_id');

            $table->decimal('discount', 64, 0)->default(0);
            $table->decimal('fee', 64, 0)->default(0);
            $table->json('extra')->nullable();

            $table->uuid('uuid')->unique();
            $table->timestamps();
            $table->softDeletesTz();

            $table->index(['from_type', 'from_id']);
            $table->index(['to_type', 'to_id']);

            $table->foreign('deposit_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade');

            $table->foreign('withdraw_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};