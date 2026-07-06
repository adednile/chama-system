<?php

namespace Tests\Unit;

use App\Services\CreditScoringEngine;
use PHPUnit\Framework\TestCase;

class CreditScoringTest extends TestCase
{
    public function test_scoring_returns_weighted_score(): void
    {
        $engine = new CreditScoringEngine();

        $score = $engine->score([
            'savings_consistency' => 8,
            'repayment_history' => 7,
            'attendance' => 9,
        ]);

        $this->assertSame(8, $score);
    }
}
