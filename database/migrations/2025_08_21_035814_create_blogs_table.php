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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->string('card_image')->nullable();
            $table->LONGTEXT('description')->nullable();
            $table->text('other_blogs_description')->nullable();
            $table->text('listing_description')->nullable();
            $table->string('slug', 255)->unique();
            $table->json('related_post_id')->nullable();
            $table->char('status',1)->default('Y');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
