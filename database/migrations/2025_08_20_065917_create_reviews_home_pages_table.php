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
        Schema::create('reviews_home_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_id');
            $table->foreign('home_id')->references('id')->on('home_page')->onDelete('cascade');
            $table->text('reviews_heading')->nullable();
            $table->text('reviews_heading_description')->nullable();
            $table->string('testimonial')->nullable();
            $table->string('reviewer_image')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_designation')->nullable();
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
        Schema::dropIfExists('reviews_home_pages');
    }
};
