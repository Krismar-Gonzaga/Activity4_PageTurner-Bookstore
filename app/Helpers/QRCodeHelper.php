<?php

namespace App\Helpers;

class QRCodeHelper
{
    public static function generate($text, $size = 200)
    {
        return 'https://chart.googleapis.com/chart?chs=' . $size . 'x' . $size . '&cht=qr&chl=' . urlencode($text) . '&choe=UTF-8';
    }
}