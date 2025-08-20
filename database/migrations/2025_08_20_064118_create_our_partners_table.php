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
        Schema::create('our_partners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_id');
            $table->foreign('home_id')->references('id')->on('home_page')->onDelete('cascade');
            $table->string('partner_image')->nullable();
            $table->text('partner_title')->nullable();
            $table->text('partner_description')->nullable();
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
        Schema::dropIfExists('our_partners');
    }
};
