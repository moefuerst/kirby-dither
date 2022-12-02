<?php

load([
    'mof\\dither\\DitherImageMagick' => 'src/DitherImageMagick.php'
], __DIR__);

Kirby\Image\Darkroom::$types['im'] = mof\dither\DitherImageMagick::class;

Kirby::plugin('mof/dither', [

    'options' => [
        'dither' => '-colorspace gray -level 3% -filter box -ordered-dither o8x8,3',
        'halftone' => '-colorspace RGB -filter box -ordered-dither h8x8a -colorspace sRGB'
    ],

    'fileMethods' => [
        'dither' => function () {
            return $this->thumb(['dither' => true]);
        },
        'halftone' => function () {
            return $this->thumb(['halftone' => true]);
        }
    ]

]);
