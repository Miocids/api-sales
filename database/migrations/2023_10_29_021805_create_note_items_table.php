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
        Schema::create('note_items', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignIdFor(\App\Models\Note::class)->constrained();
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->integer('quantity')->default(0);
            $table->decimal('total', 22, 4)->default(0);
            $table->decimal('total_usd', 22, 4)->default(0);
            $table->decimal('total_eur', 22, 4)->default(0);
            $table->string('attach');
            $table->string('created_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_items');
    }
};
