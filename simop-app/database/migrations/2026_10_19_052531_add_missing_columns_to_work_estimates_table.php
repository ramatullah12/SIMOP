
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_estimates', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->foreignId('survey_id')
                ->nullable()
                ->constrained('surveys')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('estimate_number')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('work_estimates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('survey_id');
            $table->dropConstrainedForeignId('created_by');

            $table->dropColumn([
                'estimate_number',
                'title',
                'description',
                'subtotal',
                'tax_amount',
                'total_amount',
                'status',
                'notes',
            ]);
        });
    }
};
