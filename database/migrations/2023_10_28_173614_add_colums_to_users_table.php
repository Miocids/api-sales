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
        Schema::table('users', function (Blueprint $table) {
            $table->string("username")->nullable()->after("email");
            $table->softDeletes()->after("remember_token");
            $table->boolean('status')->nullable()->after('remember_token')->default(false);
            $table->boolean('is_notify')->nullable()->after('status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(["username","deleted_at","is_notify"]);
        });
    }
};
