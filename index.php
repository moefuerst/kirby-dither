<?php

load([
    'mof\\dither\\DitherImageMagick' => 'src/DitherImageMagick.php'
], __DIR__);

Kirby\Image\Darkroom::$types['im'] = mof\dither\DitherImageMagick::class;

Kirby::plugin('mof/dither', [

    'fileMethods' => [
        'dither' => function () {
            return $this->thumb(['dither' => true]);
        },
        'halftone' => function () {
            return $this->thumb(['halftone' => true]);
        }
    ]

]);
