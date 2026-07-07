<?php

namespace App\Services;

class MpesaSMSParser
{
    public function parse(string $message): array
    {
        $normalized = trim($message);

        $transactionCode = null;
        if (preg_match('/^([A-Z0-9]{10})\b/', $normalized, $codeMatches)) {
            $transactionCode = $codeMatches[1];
        } elseif (preg_match('/Ref:\s*([A-Z0-9]+)/i', $normalized, $codeMatches)) {
            $transactionCode = $codeMatches[1];
        } else {
            preg_match('/(\b[A-Z0-9]{5,}\b)/', $normalized, $codeMatches);
            $transactionCode = $codeMatches[1] ?? null;
        }

        $amount = null;
        if (preg_match('/Ksh\s*([0-9,]+(?:\.[0-9]{1,2})?)/i', $normalized, $amountMatches)) {
            $amount = (float) str_replace(',', '', $amountMatches[1]);
        } elseif (preg_match('/Amount:\s*([0-9.]+)/i', $normalized, $amountMatches)) {
            $amount = (float) $amountMatches[1];
        } else {
            $cleanMessage = $transactionCode ? str_replace($transactionCode, '', $normalized) : $normalized;
            preg_match('/([0-9]+(?:\.[0-9]{1,2})?)/', $cleanMessage, $amountMatches);
            if (!empty($amountMatches[1])) {
                $amount = (float) $amountMatches[1];
            }
        }

        $sender = null;
        if (preg_match('/received from\s+([A-Za-z0-9\s.-]+?)(?:\s+(?:254|\+254|0)\d{9}|\s+on|\.|$)/i', $normalized, $senderMatches)) {
            $sender = $senderMatches[1];
        } elseif (preg_match('/from\s+([A-Za-z0-9\s.-]+?)(?:\.\s+Ref|\s+Ref|\.|$)/i', $normalized, $senderMatches)) {
            $sender = $senderMatches[1];
        }

        $date = null;
        if (preg_match('/on\s+(\d{4}-\d{2}-\d{2})/i', $normalized, $dateMatches)) {
            $date = $dateMatches[1];
        } elseif (preg_match('/(\d{4}-\d{2}-\d{2})/', $normalized, $dateMatches)) {
            $date = $dateMatches[1];
        } elseif (preg_match('/on\s+(\d{1,2}\/\d{1,2}\/\d{2,4})/i', $normalized, $dateMatches)) {
            $date = $dateMatches[1];
        }

        return [
            'amount' => number_format($amount ?? 0, 2, '.', ''),
            'sender' => trim((string) $sender),
            'transaction_code' => trim((string) $transactionCode),
            'date' => $date,
            'message' => $normalized,
        ];
    }
}
