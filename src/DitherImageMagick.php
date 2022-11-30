<?php
namespace mof\dither;
use Kirby\Image\Darkroom\ImageMagick;

class DitherImageMagick extends ImageMagick
{
	/**
	 * Returns additional default parameters for imagemagick
	 */
	protected function defaults(): array
	{
		return parent::defaults() + [
			'bin'       => 'convert',
			'interlace' => false,
			'dither' => false,
			'halftone' => false
		];
	}

    /**
	 * Applies the setting for dithered images
	 */
	protected function dither(string $file, array $options): string|null
	{
		if ($options['dither'] === true) {
			return '-gamma 1.1 -colorspace gray -colors 8 -ordered-dither o8x8';
		}

		return null;
	}

	/**
	 * Applies the setting for halftone images
	 */
	protected function halftone(string $file, array $options): string|null
	{
		if ($options['halftone'] === true) {
			return '-ordered-dither h8x8a';
		}

		return null;
	}

	/**
	 * Creates and runs the full imagemagick command
	 * to process the image
	 *
	 * @throws \Exception
	 */
	public function process(string $file, array $options = []): array
	{
		$options = $this->preprocess($file, $options);
		$command = [];

		$command[] = $this->convert($file, $options);
		$command[] = $this->strip($file, $options);
		$command[] = $this->interlace($file, $options);
		$command[] = $this->coalesce($file, $options);
		$command[] = $this->grayscale($file, $options);
		$command[] = $this->autoOrient($file, $options);
		$command[] = $this->resize($file, $options);
		$command[] = $this->quality($file, $options);
		$command[] = $this->dither($file, $options);
		$command[] = $this->halftone($file, $options);
		$command[] = $this->blur($file, $options);
		$command[] = $this->save($file, $options);

		// remove all null values and join the parts
		$command = implode(' ', array_filter($command));

		// try to execute the command
		exec($command, $output, $return);

		// log broken commands
		if ($return !== 0) {
			throw new Exception('The imagemagick convert command could not be executed: ' . $command);
		}

		return $options;
	}

}
