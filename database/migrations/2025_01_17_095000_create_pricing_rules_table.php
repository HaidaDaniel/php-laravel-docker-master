<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('pricing_rules', function (Blueprint $table) {
        $table->id();
        $table->string('product_code');
        $table->string('rule_name');
        $table->jsonb('rule_details'); // JSON field for rule-specific details
        $table->boolean('active')->default(true);
        $table->date('start_date')->nullable(); // Start date for the rule
        $table->date('end_date')->nullable(); // End date for the rule
        $table->jsonb('days')->nullable(); // Applicable days (e.g., ["Monday", "Tuesday"])
        $table->timestamps(); // Created_at and Updated_at timestamps
        $table->foreign('product_code')->references('code')->on('products')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing_rules');
    }
}
