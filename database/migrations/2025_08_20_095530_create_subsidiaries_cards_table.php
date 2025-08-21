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
        Schema::create('subsidiaries_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subsidiaries_id')->nullable();
            $table->foreign('subsidiaries_id')->references('id')->on('subsidiaries')->onDelete('cascade');
            $table->string('card_image')->nullable();
            $table->text('card_heading')->nullable();
            $table->text('card_description')->nullable();
            $table->text('card_url')->nullable();
            $table->char('status', 1)->default('1')->comment('Y = Active, N = inactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes()->comment('deleted_at column for soft delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidiaries_cards');
    }
};
