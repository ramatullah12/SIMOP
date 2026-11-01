
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('phone', 30);
            $table->string('email');
            $table->string('event_name');
            $table->string('event_location');
            $table->date('event_date');
            $table->text('description');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->string('status')->default('baru');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
