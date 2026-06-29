<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FinancialSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('contributions'));
        $this->assertTrue(Schema::hasTable('loans'));
        $this->assertTrue(Schema::hasTable('repayments'));
        $this->assertTrue(Schema::hasTable('fines'));
        $this->assertTrue(Schema::hasTable('transactions'));
        $this->assertTrue(Schema::hasTable('mapped_mpesa_transactions'));

        $this->assertTrue(Schema::hasColumns('contributions', ['user_id', 'chama_id', 'amount', 'contribution_date']));
        $this->assertTrue(Schema::hasColumns('loans', ['user_id', 'chama_id', 'amount', 'status', 'approved_at']));
        $this->assertTrue(Schema::hasColumns('repayments', ['loan_id', 'repayment_amount', 'repayment_date']));
        $this->assertTrue(Schema::hasColumns('fines', ['user_id', 'chama_id', 'amount', 'status']));
        $this->assertTrue(Schema::hasColumns('transactions', ['user_id', 'chama_id', 'type', 'amount', 'description']));
        $this->assertTrue(Schema::hasColumns('mapped_mpesa_transactions', ['user_id', 'amount', 'sender', 'transaction_code', 'message', 'status']));
    }
}
