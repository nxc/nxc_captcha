<?php
/**
 * @package nxcCaptcha
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Sep 2011
 **/

$Module = array(
	'name'            => 'NXC Captcha',
 	'variable_params' => true
);

$ViewList = array(
	'get' => array(
		'functions' => array( 'view' ),
		'script'    => 'get.php',
		'params'    => array(
			'classAttributeID',
			'sessionKey',
			'regenerate'
		)
	)
);

$FunctionList = array(
	'view' => array()
);
?>
