# Dither for Kirby CMS

This plugin extends [Kirby's](http://getkirby.com/) `ImageMagick` `Darkroom` driver to provide dither and halftone image manipulation.

At the moment, it is more a proof-of-concept, so there are no configuration options for the `dither` and `halftone` effects. `dither`, in `ImageMagick` terms, is set to a grayscale `o8x8` ordered dither, while `halftone` is set to a `h8x8a` ordered dither.

Please note that this plugin is not ready for production yet, since there are a few issues where it doesn't fully work with Kirby's `thumbs` component (see below).

Contributions are appreciated.


****

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-dither`. Tested with Kirby `^3.8`.

## Setup

This plugin requires that you set your [Thumbs driver](https://getkirby.com/docs/reference/system/options/thumbs#thumbs-driver) to ImageMagick (`im`) in your `config.php`:

````php
return [
  'thumbs' => [
    'driver' => 'im'
  ]
];
````

Nota bene: This plugin extends/overwrites the default `im` driver. It therefore might not play nice with plugins which also do that, such as the popular [Kirby Focus](https://github.com/flokosiol/kirby-focus).


## Use

The plugin provides two `file` methods:

`$image->dither()` and `$image->halftone()`

Furthermore, it *should* also allow you to set `thumbs` options in your `/site/config/config.php`. This does not yet work as expected all the time. I have run into issues defining [`srcset` presets](https://getkirby.com/docs/reference/system/options/thumbs#srcsets), where the `dither` and `halftone` option is ignored *if* you just define resize options. As soon as you set, e.g. `'quality' => 99` or `'grayscale' => true`, it works. 

Apply the dither effect to all images:

````php
// in your config
return [
  'thumbs' => [
    'dither' => true
  ]
];
````

Or as a preset:

````php
// in your config 
return [
    'thumbs' => [
        'presets' => [
            'default' => ['width' => 1024, 'quality' => 80],
            'dithered' => ['dither' => true],
            'halftone' => ['halftone' => true]
        ]
    ]
];

// in your template
$image->thumb('dithered');
````

Check out the Kirby docs [for more information on thumbs options](https://getkirby.com/docs/reference/system/options/thumbs).