# Dither for Kirby CMS

This plugin extends [Kirby's](http://getkirby.com/) `ImageMagick` `Darkroom` driver to provide dither and halftone image manipulation. It was loosely inspired by [Solar Web Design principles](https://github.com/lowtechmag/solar/wiki/Solar-Web-Design) and [Ditherpunk aesthetic](https://obradinn.com).

At the moment, it is more a proof-of-concept and not recommended for production.


## Caveats

- Please note that this plugin is not fully ready for production yet, since there are a few issues where it doesn't play nice with Kirby's `thumbs` component (see below, contributions are appreciated.)

- Thumb generation can get a bit computationally expensive depending on the settings handed to `ImageMagick`. It is recommended to pre-render your thumbs when using `kirby-dither`, for example using [kirby3-janitor](https://github.com/bnomei/kirby3-janitor)

- Dithering is a somewhat complex topic. What this plugin does, i.e. running a generic, pre-set `ImageMagick` command on all your images, will always be a compromise. You can get *much* better results converting your images individually with specific settings.

- If your main goal is asset-size reduction, this might not be the plugin for you. While dithered lofi-`PNG`s *can* be drastically smaller than high quality `JPG`s, they are more of a statement. In most use cases, modern image formats such as `JPEG XL`, `AVIF` or `WebP` are a better option.


****


## Installation

### Prerequisites
Tested with Kirby `3.8` and up.

Nota bene: This plugin extends Kirby’s default `im`  `Darkroom` driver. It therefore might not play nice with plugins which also do that, such as the popular [Kirby Focus](https://github.com/flokosiol/kirby-focus).

### Download
Download and copy this repository to `/site/plugins/kirby-dither`.


## Setup

It is recommended to fully clean your `/media` folder after installation, since already existing thumbs might not be overwritten.

For the plugin to work, you neet to set your [Thumbs driver](https://getkirby.com/docs/reference/system/options/thumbs#thumbs-driver) to ImageMagick (`im`) in your `config.php`:

````php
return [
  'thumbs' => [
    'driver' => 'im'
  ]
];
````


## Use

`kirby-dither` provides two [`file` methods](https://getkirby.com/docs/reference/objects/cms/file), `dither()` and `halftone()`.

Use them in your template:

````html
// dither
<img src="<?= $image->dither()->url() ?>" alt="">

// halftone effect
<img src="<?= $image->halftone()->url() ?>" alt="">
````

Furthermore, it *should* also allow you to set `thumbs` options in your `/site/config/config.php`. However, this does not yet work as expected all the time. I have run into issues defining [`srcset` presets](https://getkirby.com/docs/reference/system/options/thumbs#srcsets), where the `dither` and `halftone` option is ignored if you just define resize options. As soon as you set, e.g. `'quality' => 99` or `'grayscale' => true`, it works.

### Config Example
Apply dither to all images for a full ditherpunk experience. Depending on your dithering settings (see below), it is recommended to heavily resize images, and use `png` or `gif` as output format for a reduced file size:

````php
// in your config
return [
  'thumbs' => [ // ditherpunk galore
    'driver' => 'im',
    'height' => 800,
    'quality' => 96,
    'width' => 800,
    'format' => 'png',
    'dither' => true
  ]
];
````

Or define a preset:

````php 
// in your config
return [
  'thumbs' => [
    'driver' => 'im',
    'presets' => [
      'halftone' => [
        'height' => 800,
        'quality' => 96,
        'width' => 800,
        'format' => 'png',
        'halftone' => true
      ]
    ]
  ]
];

// in your template
$image->thumb('halftone');
````

Check out the Kirby docs [for more information on thumbs options](https://getkirby.com/docs/reference/system/options/thumbs).

### Options 
The defaults apply a rather over-the-top, unapologetic ‘effect’. However, you can fully control how `ImageMagick` manipulates your images by setting an option in your `config.php`:

````php
'mof.dither' => [
  'dither' => '-colors 256 -dither FloydSteinberg',
  'halftone' => '-monochrome -ordered-dither h4x4a'
],
````

Consult the [ImageMagick documentation](https://legacy.imagemagick.org/Usage/quantize/) for more information on what you could do. Note that Kirby's `thumbs` component constructs the `ImageMagick` command, so you should only set color quantization and dithering specific commands in this option. Resizing, output file format, etc. are best defined in your `thumbs` config (see above).


### Some front-end ideas

With some dithering settings, it might be beneficial to enable specific image scaling algorithms using CSS:

````css
img.dithered {
    image-rendering:optimizeSpeed;             /* Fallback */
    image-rendering:-moz-crisp-edges;          /* FF */
    image-rendering:-webkit-optimize-contrast; /* Safari */
    image-rendering:optimize-contrast;         /* CSS3 spec */
    image-rendering:crisp-edges;               /* CSS4 spec */
    image-rendering:pixelated;                 /* CSS4 spec */
    -ms-interpolation-mode:nearest-neighbor;   /* IE8+ */
}
````

Grayscale images can be ‘recolored’ using [CSS blend-modes](https://developer.mozilla.org/en-US/docs/Web/CSS/mix-blend-mode) such as `hard-light` for a nice effect, as seen on [Lowtechmag](https://solar.lowtechmagazine.com). Note that you need a wrapper around `img` tags, as coloring the `img` background does not work:

````css
// CSS
.background-img {
	background-blend-mode: hard-light;
	background-color: #333319;
}

img {
	mix-blend-mode: hard-light;
}

figure {
	background-color: #333319;
}
````

````html
// HTML
<div class="background-img" style="background-image: url('path/to/image')"></div>

<figure><img alt="" src="path/to/image"/></figure>
````