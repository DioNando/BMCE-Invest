<?php

use App\Enums\Status;
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
        Schema::create('time_slot_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_slot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('investor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('issuer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('status', array_column(Status::cases(), 'value'))->default(Status::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slot_attendees');
    }
};
