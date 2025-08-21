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
        Schema::create('events', function (Blueprint $table) {
           $table->id();
            $table->text('event_title')->nullable();
            $table->string('event_hero_image')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->string('event_card_image')->nullable();
            $table->longText('event_description')->nullable();
            $table->text('other_event_description')->nullable();
            $table->text('event_listing_description')->nullable();
            $table->string('slug', 255)->unique();
            $table->json('event_related_post_id')->nullable();
            $table->char('status', 1)->default('Y');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
