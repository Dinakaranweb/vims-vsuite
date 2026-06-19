<?php

if (!function_exists('indianCurrencyFormat')) {
    function indianCurrencyFormat($number) {
        // Store original value for non-numeric cases
        $original = $number;
        
        // Convert string input to float/number
        if (is_string($number)) {
            // Remove any commas and extra spaces, then check if numeric
            $cleaned = str_replace([',', ' '], '', trim($number));
            if (is_numeric($cleaned)) {
                $number = (float)$cleaned;
            } else {
                // Return original string if not numeric
                return $original;
            }
        } elseif (!is_numeric($number)) {
            // Return original value if not numeric
            return $original;
        }
        
        // Check if the number has decimal places
        $hasDecimals = (fmod($number, 1) != 0);
        
        if ($hasDecimals) {
            // Number has decimals, format with 2 decimal places
            $number = round($number, 2);
            $parts = explode('.', (string)$number);
            
            $wholePart = (int)$parts[0];
            $decimalPart = isset($parts[1]) ? $parts[1] : '00';
            
            // Pad decimal part with zero if needed
            if (strlen($decimalPart) == 1) {
                $decimalPart .= '0';
            }
            
            // Handle negative whole numbers
            $isNegative = false;
            if ($wholePart < 0) {
                $isNegative = true;
                $wholePart = abs($wholePart);
            }
            
            // Format the whole part
            $lastThree = substr($wholePart, -3);
            $restUnits = substr($wholePart, 0, -3);
            
            if ($restUnits != '') {
                $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
            }
            
            $formatted = $restUnits . ($restUnits ? ',' : '') . $lastThree;
            $formatted = $isNegative ? '-' . $formatted : $formatted;
            
            return $formatted . '.' . $decimalPart;
        } else {
            // Number is whole, format without decimals
            $number = (int)$number;
            
            if ($number == 0) {
                return '0';
            }
            
            $isNegative = false;
            if ($number < 0) {
                $isNegative = true;
                $number = abs($number);
            }
            
            $lastThree = substr($number, -3);
            $restUnits = substr($number, 0, -3);
            
            if ($restUnits != '') {
                $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
            }
            
            $formatted = $restUnits . ($restUnits ? ',' : '') . $lastThree;
            return $isNegative ? '-' . $formatted : $formatted;
        }
    }
}
