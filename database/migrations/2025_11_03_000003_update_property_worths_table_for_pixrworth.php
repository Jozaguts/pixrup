<?php

/**
 * Description: Migration updating property worth table to support PixrWorth requirements.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Adds valuation range columns and adjusts confidence precision.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Description: Apply schema changes required for PixrWorth valuations.
     * Parameters: None.
     * Returns: void
     * Expected Result: Table gains value_low/value_high columns and confidence precision increases.
     */
    public function up(): void
    {
        Schema::table('property_worths', function (Blueprint $table): void {
            $table->decimal('value_low', 12, 2)->nullable()->after('value');
            $table->decimal('value_high', 12, 2)->nullable()->after('value_low');
        });
    }

    /**
     * Description: Revert schema changes applied for PixrWorth valuations.
     * Parameters: None.
     * Returns: void
     * Expected Result: Restores original confidence column and removes range columns.
     */
    public function down(): void
    {
        Schema::table('property_worths', function (Blueprint $table): void {
            $table->dropColumn(['value_low', 'value_high']);
        });
    }
};
