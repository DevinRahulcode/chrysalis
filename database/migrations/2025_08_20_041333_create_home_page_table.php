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
        Schema::create('home_page', function (Blueprint $table) {
            $table->id();
            $table->string('linkedin_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('x_link')->nullable();
            //about section
            $table->string('about_section_heading')->nullable();
            $table->text('about_section_description')->nullable();

            //oOur Business Solutions
            $table->string('our_business_heading')->nullable();
            $table->text('our_business_description')->nullable();
            $table->string('icon_one')->nullable();
            $table->string('text_one')->nullable();
            $table->string('icon_two')->nullable();
            $table->string('text_two')->nullable();
            $table->string('icon_three')->nullable();
            $table->string('text_three')->nullable();
            $table->string('icon_four')->nullable();
            $table->string('text_four')->nullable();
            $table->string('icon_five')->nullable();
            $table->string('text_five')->nullable();
            $table->string('icon_six')->nullable();
            $table->string('text_six')->nullable();
            $table->string('icon_seven')->nullable();
            $table->string('text_seven')->nullable();

            //Your Contribution
            $table->string('image_icon')->nullable();
            $table->string('your_contribution_heading')->nullable();
            $table->text('your_contribution_description')->nullable();
            $table->string('your_contribution_image')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page');
    }
};
