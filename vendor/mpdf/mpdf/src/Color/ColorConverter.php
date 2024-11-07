<?php

namespace Mpdf\Color;

use Mpdf\Mpdf;

class ColorConverter
{
	const MODE_GRAYSCALE = 1;
	const MODE_SPOT = 2;
	const MODE_RGB = 3;
	const MODE_CMYK = 4;
	const MODE_RGBA = 5;
	const MODE_CMYKA = 6;

	private $mpdf;
	private $colorModeConverter;
	private $colorSpaceRestrictor;
	private $cache;

	public function __construct(Mpdf $mpdf, ColorModeConverter $colorModeConverter, ColorSpaceRestrictor $colorSpaceRestrictor)
	{
		$this->mpdf = $mpdf;
		$this->colorModeConverter = $colorModeConverter;
		$this->colorSpaceRestrictor = $colorSpaceRestrictor;
		$this->cache = [];
	}

	public function convert($color, array &$PDFAXwarnings = [])
	{
		$color = strtolower(trim($color));

		if ($color === 'transparent' || $color === 'inherit') {
			return false;
		}

		if (isset(NamedColors::$colors[$color])) {
			$color = NamedColors::$colors[$color];
		}

		if (!isset($this->cache[$color])) {
			$c = $this->convertPlain($color, $PDFAXwarnings);
			$cstr = '';
			if (is_array($c)) {
				$c = array_pad($c, 6, 0);
				$cstr = pack('a1ccccc', $c[0], $c[1] & 0xFF, $c[2] & 0xFF, $c[3] & 0xFF, $c[4] & 0xFF, $c[5] & 0xFF);
			}

			$this->cache[$color] = $cstr;
		}

		return $this->cache[$color];
	}

	public function lighten($c)
	{
		$this->ensureBinaryColorFormat($c);

		if ($c[0] == static::MODE_RGB || $c[0] == static::MODE_RGBA) {
			list($h, $s, $l) = $this->colorModeConverter->rgb2hsl(ord($c[1]) / 255, ord($c[2]) / 255, ord($c[3]) / 255);
			$l += ((1 - $l) * 0.8);
			list($r, $g, $b) = $this->colorModeConverter->hsl2rgb($h, $s, $l);
			$ret = [3, $r, $g, $b];
		} elseif ($c[0] == static::MODE_CMYK || $c[0] == static::MODE_CMYKA) {
			$ret = [4, max(0, ord($c[1]) - 20), max(0, ord($c[2]) - 20), max(0, ord($c[3]) - 20), max(0, ord($c[4]) - 20)];
		} elseif ($c[0] == static::MODE_GRAYSCALE) {
			$ret = [1, min(255, ord($c[1]) + 32)];
		}

		$c = array_pad($ret, 6, 0);
		$cstr = pack('a1ccccc', $c[0], $c[1] & 0xFF, $c[2] & 0xFF, $c[3] & 0xFF, $c[4] & 0xFF, $c[5] & 0xFF);

		return $cstr;
	}

	public function darken($c)
	{
		$this->ensureBinaryColorFormat($c);

		if ($c[0] == static::MODE_RGB || $c[0] == static::MODE_RGBA) {
			list($h, $s, $l) = $this->colorModeConverter->rgb2hsl(ord($c[1]) / 255, ord($c[2]) / 255, ord($c[3]) / 255);
			$s *= 0.25;
			$l *= 0.75;
			list($r, $g, $b) = $this->colorModeConverter->hsl2rgb($h, $s, $l);
			$ret = [3, $r, $g, $b];
		} elseif ($c[0] == static::MODE_CMYK || $c[0] == static::MODE_CMYKA) {
			$ret = [4, min(100, ord($c[1]) + 20), min(100, ord($c[2]) + 20), min(100, ord($c[3]) + 20), min(100, ord($c[4]) + 20)];
		} elseif ($c[0] == static::MODE_GRAYSCALE) {
			$ret = [1, max(0, ord($c[1]) - 32)];
		}

		$c = array_pad($ret, 6, 0);
		$cstr = pack('a1ccccc', $c[0], $c[1] & 0xFF, $c[2] & 0xFF, $c[3] & 0xFF, $c[4] & 0xFF, $c[5] & 0xFF);

		return $cstr;
	}

	/**
	 * @param string $c
	 * @return float[]
	 */
	public function invert($c)
	{
		$this->ensureBinaryColorFormat($c);

		if ($c[0] == static::MODE_RGB || $c[0] == static::MODE_RGBA) {
			return [3, 255 - ord($c[1]), 255 - ord($c[2]), 255 - ord($c[3])];
		}

		if ($c[0] == static::MODE_CMYK || $c[0] == static::MODE_CMYKA) {
			return [4, 100 - ord($c[1]), 100 - ord($c[2]), 100 - ord($c[3]), 100 - ord($c[4])];
		}

		if ($c[0] == static::MODE_GRAYSCALE) {
			return [1, 255 - ord($c[1])];
		}

		// Cannot cope with non-RGB colors at present
		throw new \Mpdf\MpdfException('Trying to invert non-RGB color');
	}

	/**
	 * @param string $c Binary color string
	 *
	 * @return string
	 */
	public function colAtoString($c)
	{
		if ($c[0] == static::MODE_GRAYSCALE) {
			return 'rgb(' . ord($c[1]) . ', ' . ord($c[1]) . ', ' . ord($c[1]) . ')';
		}

		if ($c[0] == static::MODE_SPOT) {
			return 'spot(' . ord($c[1]) . ', ' . ord($c[2]) . ')';
		}

		if ($c[0] == static::MODE_RGB) {
			return 'rgb(' . ord($c[1]) . ', ' . ord($c[2]) . ', ' . ord($c[3]) . ')';
		}

		if ($c[0] == static::MODE_CMYK) {
			return 'cmyk(' . ord($c[1]) . ', ' . ord($c[2]) . ', ' . ord($c[3]) . ', ' . ord($c[4]) . ')';
		}

		if ($c[0] == static::MODE_RGBA) {
			return 'rgba(' . ord($c[1]) . ', ' . ord($c[2]) . ', ' . ord($c[3]) . ', ' . sprintf('%0.2F', ord($c[4]) / 100) . ')';
		}

		if ($c[0] == static::MODE_CMYKA) {
			return 'cmyka(' . ord($c[1]) . ', ' . ord($c[2]) . ', ' . ord($c[3]) . ', ' . ord($c[4]) . ', ' . sprintf('%0.2F', ord($c[5]) / 100) . ')';
		}

		return '';
	}

	/**
	 * @param string $color
	 * @param string[] $PDFAXwarnings
	 *
	 * @return bool|float[]
	 */
	private function convertPlain($color, array &$PDFAXwarnings = [])
	{
		$c = false;

		if (preg_match('/^[\d]+$/', $color)) {
			$c = [static::MODE_GRAYSCALE, $color]; // i.e. integer only
		} elseif (strpos($color, '#') === 0) { // case of #nnnnnn or #nnn
			if (strlen($color) === 4) {
				$c = [static::MODE_RGB,
					hexdec($color[1] . $color[1]),
					hexdec($color[2] . $color[2]),
					hexdec($color[3] . $color[3])];
			} elseif (strlen($color) === 7) {
				$c = [static::MODE_RGB,
					hexdec($color[1] . $color[2]),
					hexdec($color[3] . $color[4]),
					hexdec($color[5] . $color[6])];
			}
		}

		return $c;
	}

	/**
	 * @param $c
	 */
	private function ensureBinaryColorFormat(&$c)
	{
		if (!is_string($c)) {
			throw new \Mpdf\MpdfException('Color is not in binary format');
		}
	}
}
