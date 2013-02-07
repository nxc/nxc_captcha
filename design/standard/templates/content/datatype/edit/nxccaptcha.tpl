{ezcss_require( array( 'nxc.captcha.css' ) )}
{ezscript_require( array( 'nxc.captcha.js' ) )}

{def $class_content = $attribute.contentclass_attribute.content}

{if eq( $attribute.data_int, 0 )}
	<p>{'To prevent spamming, please enter the secure code you see in the image below in the input box beneath the image.'|i18n( 'extension/nxc_captcha' )}</p>
	<img id="nxc-captcha-{$attribute.id}" alt="{'Secure code'|i18n( 'extension/nxc_captcha' )}" title="{'Secure code'|i18n( 'extension/nxc_captcha' )}" src="{concat( 'nxc_captcha/get/', $attribute.contentclass_attribute.id, '/nxc_captcha_object_attribute_', $attribute.id, '/0' )|ezurl( 'no' )}" />
	<p>
		<input class="captcha-input" id="nxc-captcha-input-{$attribute.id}" type="text" name="nxc_captcha_{$attribute.id}" value="{$attribute.data_text}" size="{$class_content.length.value}" maxlength="{$class_content.length.value}" />
		<a href="{concat( 'nxc_captcha/get/', $attribute.contentclass_attribute.id, '/nxc_captcha_object_attribute_', $attribute.id, '/1' )|ezurl( 'no' )}" class="nxc-captcha-regenerate" id="nxc-captcha-regenerate-{$attribute.id}">{'Regenerate'|i18n( 'extension/nxc_captcha' )}</a>
	</p>
{else}
	<p>{'Secure code is allready entered'|i18n( 'extension/nxc_captcha' )}</p>
{/if}

{undef $class_content}
