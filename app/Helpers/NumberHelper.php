<?php

namespace App\Helpers;

class NumberHelper
{
    public static function numberToWords($number)
    {
        $hyphen      = '-';
        $dictionary  = [
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            100000 => 'Lakh',
            10000000 => 'Crore'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 21) {
            return $dictionary[$number];
        }

        if ($number < 100) {
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            return $dictionary[$tens] . ($units ? $hyphen . $dictionary[$units] : '');
        }

        $string = '';
        $places = [
            10000000 => 'Crore',
            100000   => 'Lakh',
            1000     => 'Thousand',
            100      => 'Hundred'
        ];

        foreach ($places as $value => $name) {
            if ($number >= $value) {
                $count = (int)($number / $value);
                $number %= $value;
                $string .= self::numberToWords($count) . ' ' . $name;
                if ($number) {
                    $string .= ' ';
                }
            }
        }

        if ($number > 0) {
            $string .= self::numberToWords($number);
        }

        return trim($string);
    }

    public static function amountInWords($amount)
    {
        $amountInt = (int)$amount;
        $amountDec = round(($amount - $amountInt) * 100);

        $words = self::numberToWords($amountInt) . ' Rupees';
        if ($amountDec > 0) {
            $words .= ' and ' . self::numberToWords($amountDec) . ' Paise';
        }
        return $words . ' Only';
    }

    public static function formatIndianCurrency($number)
    {
        // Sabse pehle check karein ki 'intl' extension hai ya nahi
        if (class_exists('NumberFormatter')) {
            $formatter = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
            // Agar number valid nahi hai, to 0.00 return karein
            if (!is_numeric($number)) {
                return $formatter->formatCurrency(0, 'INR');
            }
            return $formatter->formatCurrency($number, 'INR');
        }

        // Fallback: Agar 'intl' extension nahi hai to basic formatting
        $number = (float)$number;
        $decimal = round($number - floor($number), 2) * 100;
        $decimal_part = sprintf('%02d', $decimal);
        $integer = floor($number);
        $string = (string)$integer;
        $len = strlen($string);
        if ($len <= 3) {
            return '₹' . $string . '.' . $decimal_part;
        }
        $last_three = substr($string, -3);
        $rest = substr($string, 0, -3);
        $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
        return '₹' . $rest . ',' . $last_three . '.' . $decimal_part;
    }
}
