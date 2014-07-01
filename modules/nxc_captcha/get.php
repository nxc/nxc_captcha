<?php
/**
 * @package nxcCaptcha
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Sep 2011
 **/
 
/**
 * Updated to use -1 value for nxc_capture.ini defaults
 * @author  David Sayre <david@sayre.com>
 * @date	04 Feb 2014
 **/
 
$params  = array();
if ($Params['classAttributeID'] == -1 ) {
	// Generate from INI Defaults
	$ini = eZINI::instance( 'nxc_captcha.ini' );
	$params['length'] = $ini->variable( 'CaptchaSettings', 'Length' );
	$params['type'] = $ini->variable( 'CaptchaSettings', 'Type' );
	$params['width'] = $ini->variable( 'CaptchaSettings', 'Width' );
	$params['height'] = $ini->variable( 'CaptchaSettings', 'Height' );
	$params['noise_level'] = $ini->variable( 'CaptchaSettings', 'NoiseLevel' );
	$params['text_tilt_angle'] = $ini->variable( 'CaptchaSettings', 'TextTiltAngle' );
	$params['characters_color'] = $ini->variable( 'CaptchaSettings', 'CharactersColor' );
	$params['background_color'] = $ini->variable( 'CaptchaSettings', 'BackgroundColor' );
	$params['noise_color'] = $ini->variable( 'CaptchaSettings', 'NoiseColor' );
	$params['skip_user_ids'] = $ini->variable( 'CaptchaSettings', 'SkipUserIds' );
	$params['skip_role_ids'] = $ini->variable( 'CaptchaSettings', 'SkipRoleIds' );
	$params['exclude_characters'] = $ini->variable( 'CaptchaSettings', 'ExcludeCharacters' );
} else {
	// Generate from Object Attribute
	$attribute = eZContentClassAttribute::fetch( $Params['classAttributeID'] );
	if( $attribute instanceof eZContentClassAttribute === false ) {
		echo ezpI18n::tr(
			'extension/nxc_captcha',
			'Can`t fetch class attribute (ID: %id)',
			null,
			array(
				'%id' => htmlspecialchars( $Params['classAttributeID'], ENT_QUOTES, 'UTF-8' )
			)
		);
		eZExecution::cleanExit();
	}
	$content = $attribute->attribute( 'content' );
	foreach( $content as $option => $info ) {
		if( isset( $info['value'] ) ) {
			$params[ $option ] = $info['value'];
		}
	}
}

if( isset( $params['exclude_characters'] ) ) {
	$params['exclude_characters'] = explode( ',', $params['exclude_characters'] );
}

$captcha = new nxcCaptcha(
	$params,
	$Params['sessionKey'],
	( (int) $Params['regenerate'] === 1 )
);
$captcha->createImage();
$captcha->output();
eZExecution::cleanExit();
?>
