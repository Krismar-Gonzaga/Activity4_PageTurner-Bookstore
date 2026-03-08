<?php

if (!function_exists('qr_code')) {
    function qr_code($text, $size = 200)
    {
        return App\Helpers\QRCodeHelper::generate($text, $size);
    }
}