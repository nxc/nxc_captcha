{if eq( $attribute.content.checked, true() )}
{'Captcha secure code was entered'|i18n( 'extension/nxc_captcha' )}: {$attribute.content.captcha_code}
{else}
{'Captcha secure code wasn`t entered'|i18n( 'extension/nxc_captcha' )}
{/if}
