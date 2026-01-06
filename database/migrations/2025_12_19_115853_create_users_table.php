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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('member_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('language')->nullable();
            $table->integer('like_status')->default(0);
            $table->integer('fav_user_status')->default(0);
            $table->string('gender')->nullable();
            $table->string('profile_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->nullable();
            $table->integer('rejection_email_status')->default(0);
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('email_verification_otp')->nullable();
            $table->integer('status')->default(1);
            $table->integer('admin_status')->default(0);
            $table->timestamp('admin_approve_time')->nullable();
            $table->integer('profile_approval')->default(0);
            $table->string('profie_rating_status')->default('OUT');
            $table->string('profile_image')->nullable();
            $table->string('online_status')->nullable();
            $table->date('dob')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('body_type')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('nationality')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('sexual_orientation')->nullable();
            $table->string('education')->nullable();
            $table->string('field_of_work')->nullable();
            $table->string('relationship_status')->nullable();
            $table->string('zodiac_sign')->nullable();
            $table->string('smoking')->nullable();
            $table->string('drinking')->nullable();
            $table->string('tattoos')->nullable();
            $table->string('piercings')->nullable();
            $table->longText('about_me')->nullable();
            $table->longText('about_match')->nullable();
            $table->integer('hear_about_us')->nullable();
            $table->string('looking_for')->nullable();
            $table->string('remember_token')->nullable();

            $table->integer('children')->nullable();
            $table->integer('exercise')->nullable();
            $table->integer('pets')->nullable();
            $table->integer('income_level')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
