<?php
/**
 * @package nxcCaptcha
 * @class   nxcCaptcha
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Sep 2011
 **/

class nxcCaptcha
{
	private $params        = array();
	private $image         = null;
	private $sessionKey    = 'captcha';
	private $regenerate    = true;
	private $colors        = array();
	private $font          = 'extension/nxc_captcha/design/standard/fonts/captcha_font.ttf';

	public function __construct(
		array $params = array(),
		$sessionKey = null,
		$regenerate = false
	) {
		$defaultParams = array();
		foreach( nxcCaptchaType::$definition as $option => $info ) {
			if( isset( $info['default'] ) ) {
				$defaultParams[ $option ] = $info['default'];
			}
		}

		$this->params = array_merge( $defaultParams, $params );
		if( $this->hasParameter( 'font_size' ) === false ) {
			$this->setParameter( 'font_size', (int) $this->getParameter( 'height' ) * 0.6 );
		}

		if( $sessionKey !== null ) {
			$this->sessionKey = $sessionKey;
		}
		$this->regenerate = $regenerate;
	}

	public function createImage() {
		$this->image = imagecreate(
			$this->getParameter( 'width' ),
			$this->getParameter( 'height' )
		);

		// Set colors
		$colorParams = array(
			'background_color',
			'characters_color',
			'noise_color'
		);
		foreach( $colorParams as $name ) {
			$value = $this->getParameter( $name );
			$name  = str_replace( '_color', '', $name );

			$this->colors[ $name ] = imagecolorallocate(
				$this->image,
				hexdec( substr( $value, 0, 2 ) ),
				hexdec( substr( $value, 2, 2 ) ),
				hexdec( substr( $value, 4, 2 ) )
			);
		}

		// Generate noise
		$noiseNumber = $this->getParameter( 'width' ) * $this->getParameter( 'height' ) / 2000;
		for( $i = 0; $i < $noiseNumber; $i++ ) {
			$radius = $this->getParameter( 'noise_level' ) / 10 * 3 * mt_rand( 1, 5 );
			imagefilledellipse(
				$this->image,
				mt_rand( 0, $this->getParameter( 'width' ) ),
				mt_rand( 0, $this->getParameter( 'height' ) ),
				$radius,
				$radius,
				$this->colors['noise']
			);
		}

		// Fix text font size
		$text = $this->getText();
		$c    = 0;
		do {
			$textbox = imagettfbbox(
				$this->getParameter( 'font_size' ),
				0,
				$this->font,
				$text
			);

			$this->setParameter(
				'font_size',
				$this->getParameter( 'font_size' ) - 1
			);
			$c++;
			if( $c > 20 ) {
				break;
			}
		} while (
			$textbox[4] > $this->getParameter( 'width' ) - $this->getParameter( 'font_size' ) * 1.2
		);

		// Draw the text
		$x = ( $this->getParameter( 'width' ) - $textbox[4] ) / 2;
		$y = ( $this->getParameter( 'height' ) - $textbox[5] ) / 2;
		$l = (int) strlen( $text );
		for( $i = 0; $i < $l; $i++ ) {
			$angle = rand(
				-1 * $this->getParameter( 'text_tilt_angle' ),
				$this->getParameter( 'text_tilt_angle' )
			);
			$ret = imagettftext(
				$this->image,
				$this->getParameter( 'font_size' ),
				$angle,
				$x,
				min( (int) $this->getParameter( 'height' ), $y ),
				$this->colors['characters'],
				$this->font,
				$text[ $i ]
			);
			$x = $ret[2];
		}
	}

	private function getText() {
		$http = eZHTTPTool::instance();

		if(
			$this->regenerate ||
			$http->hasSessionVariable( $this->sessionKey ) === false ||
			$http->sessionVariable( $this->sessionKey ) === null
		) {
			$charsTable = $this->getCharacterTable();
			if(
				isset( $this->params['exclude_characters'] )
				&& is_array( $this->params['exclude_characters'] )
			) {
				foreach( $this->params['exclude_characters'] as $char ) {
					$key = array_search( trim( $char ), $charsTable );
					if( $key !== false ) {
						unset( $charsTable[ $key ] );
					}
				}
			}

			$text   = null;
			$length = (int) $this->getParameter( 'length' );
			for( $i = 0; $i < $length; $i++ ) {
				$text .= $charsTable[ array_rand( $charsTable ) ];
			}
			$http->setSessionVariable( $this->sessionKey, $text );

			return $text;
		} else {
			return $http->sessionVariable( $this->sessionKey );
		}
	}

	private function getCharacterTable() {
		switch( $this->params['type'] ) {
			case nxcCaptchaType::TYPE_ALPHANUMERIC:
				return array_merge( range( 'A', 'Z' ), range( 0, 9 ) );
			case nxcCaptchaType::TYPE_NUMERIC:
				return range( 0, 9 );
			case nxcCaptchaType::TYPE_ALPHABETIC:
				return range( 'A', 'Z' );
			default:
				return array_merge( range( 'A', 'Z' ), range( 0, 9 ) );
		}
	}

	public function output() {
		header( 'Content-Type: image/gif' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		imagegif( $this->image );
		imagedestroy( $this->image );
	}

	public function hasParameter( $name ) {
		return isset( $this->params[ $name ] );
	}

	public function getParameter( $name ) {
		return isset( $this->params[ $name ] ) ? $this->params[ $name ] : null;
	}

	public function setParameter( $name, $value ) {
		$this->params[ $name ] = $value;
	}
}
?>
