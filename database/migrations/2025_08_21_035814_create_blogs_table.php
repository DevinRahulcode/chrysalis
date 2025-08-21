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
            $table->text('blog_title')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->string('date')->nullable();
            $table->string('thumbnail')->nullable();
            $table->LONGTEXT('news_description')->nullable();
            $table->char('status')->default('Y');
            $table->string('slug', 255)->unique();
          

            // SEO fields
            $table->string('page_title')->nullable();
            $table->string('description')->nullable();
            $table->string('keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();


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
