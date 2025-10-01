<?php

use Carbon\Carbon;

if (!function_exists('format_date')) {
    function format_date($date, $format = 'M d, Y H:i') {
        if (!$date) {
            return 'N/A';
        }
        
        if ($date instanceof Carbon) {
            return $date->format($format);
        }
        
        return Carbon::parse($date)->format($format);
    }
}