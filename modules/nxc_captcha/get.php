<?php
/**
 * @package nxcCaptcha
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Sep 2011
 **/

$attribute = eZContentClassAttribute::fetch( $Params['classAttributeID'] );
if( $attribute instanceof eZContentClassAttribute === false ) {
	echo ezpI18n::tr(
		'extension/nxc_captcha',
		'Can`t fetch class attribute (ID: %id)',
		null,
		array(
			'%id' => $Params['classAttributeID']
		)
	);
	eZExecution::cleanExit();
}

foreach( nxcCaptchaType::$definition as $option => $info ) {
	if( $attribute->hasAttribute( $info['field'] ) ) {
		$params[ $option ] = $attribute->attribute( $info['field'] );
	}
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
