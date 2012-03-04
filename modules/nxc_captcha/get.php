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

$content = $attribute->attribute( 'content' );
$params  = array();
foreach( $content as $option => $info ) {
	if( isset( $info['value'] ) ) {
		$params[ $option ] = $info['value'];
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
