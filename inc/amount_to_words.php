<?php

function adexAmountInWords($amount)
{
    $amount = number_format((float) $amount, 2, '.', '');
    list($rupees, $paise) = explode('.', $amount);
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    $underThousand = function ($number) use ($ones, $tens) {
        $words = '';
        if ($number >= 100) { $words .= $ones[(int) ($number / 100)].' Hundred '; $number %= 100; }
        if ($number >= 20) { $words .= $tens[(int) ($number / 10)].' '; $number %= 10; }
        if ($number > 0) { $words .= $ones[$number].' '; }
        return trim($words);
    };
    $parts = [];
    $rupees = (int) $rupees;
    if ($rupees >= 10000000) { $parts[] = $underThousand((int) ($rupees / 10000000)).' Crore'; $rupees %= 10000000; }
    if ($rupees >= 100000) { $parts[] = $underThousand((int) ($rupees / 100000)).' Lakh'; $rupees %= 100000; }
    if ($rupees >= 1000) { $parts[] = $underThousand((int) ($rupees / 1000)).' Thousand'; $rupees %= 1000; }
    if ($rupees > 0) { $parts[] = $underThousand($rupees); }
    $words = $parts ? implode(' ', $parts) : 'Zero';
    $paiseWords = $paise > 0 ? $underThousand((int) $paise) : 'Zero';
    return $words.' Rupees and '.$paiseWords.' Paisa only';
}
