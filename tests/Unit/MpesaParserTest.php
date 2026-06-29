<?php

namespace Tests\Unit;

use App\Services\MpesaSMSParser;
use PHPUnit\Framework\TestCase;

class MpesaParserTest extends TestCase
{
    public function test_parser_extracts_amount_sender_code_and_date(): void
    {
        $parser = new MpesaSMSParser();
        $message = 'Thank you for buying airtime. Amount: 2500.00 from John Doe. Ref: QWERTY. Date: 2026-06-15';

        $result = $parser->parse($message);

        $this->assertSame('2500.00', $result['amount']);
        $this->assertSame('John Doe', $result['sender']);
        $this->assertSame('QWERTY', $result['transaction_code']);
        $this->assertSame('2026-06-15', $result['date']);
    }

    public function test_parser_extracts_sent_to_transactions(): void
    {
        $parser = new MpesaSMSParser();
        $message = 'UFLRS8HSMK Confirmed. Ksh50.00 sent to Consolata Shrine for account Offertory on 21/6/26 at 11:02 AM New M-PESA balance is Ksh957.72. Transaction cost, Ksh0.00.Amount you can transact within the day is 499,950.00. Download My OneApp on https://saf.cx/kWQpy';

        $result = $parser->parse($message);

        $this->assertSame('50.00', $result['amount']);
        $this->assertSame('To: Consolata Shrine', $result['sender']);
        $this->assertSame('UFLRS8HSMK', $result['transaction_code']);
        $this->assertSame('21/6/26', $result['date']);
    }
}
