{ezcss_require( array( 'nxc.captcha.css' ) )}

{def $content = $class_attribute.content}

<div class="captcha_view">
	<div class="block">
		<div class="element">
			<label>{'Length (characters)'|i18n( 'extension/nxc_captcha' )}:</label>
			<p>{$content.length.value}</p>
		</div>
		<div class="element">
			<label>{'Type'|i18n( 'extension/nxc_captcha' )}:</label>
			{foreach $content.type.possible_values as $type_id => $type}
			{if eq( $type_id, $content.type.value )}<p>{$type}</p>{/if}
			{/foreach}
		</div>
		<div class="clear"></div>
	</div>

	<div class="block">
		<div class="element">
			<label>{'Width'|i18n( 'extension/nxc_captcha' )}:</label>
			<p>{$content.width.value}px</p>
		</div>
		<div class="element">
			<label>{'Height'|i18n( 'extension/nxc_captcha' )}:</label>
			<p>{$content.height.value}px</p>
		</div>
		<div class="element">
			<label>{'Noise level'|i18n( 'extension/nxc_captcha' )}:</label>
			<p>{$content.noise_level.value}%</p>
		</div>
		<div class="element">
			<label>{'Max text tilt angle'|i18n( 'extension/nxc_captcha' )}:</label>
			<p>{$content.text_tilt_angle.value}&deg;</p>
		</div>
		<div class="clear"></div>
	</div>

	<div class="block">
		<label>{'Colors'|i18n( 'extension/nxc_captcha' )}</label>
		<div class="element">
			{'Characters'|i18n( 'extension/nxc_captcha' )}:
			<div class="nxc-captcha-color-selector-disabled">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.characters_color.value}"></div>
			</div>
		</div>
		<div class="element">
			{'Background'|i18n( 'extension/nxc_captcha' )}:
			<div class="nxc-captcha-color-selector-disabled">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.background_color.value}"></div>
			</div>
		</div>
		<div class="element">
			{'Noise'|i18n( 'extension/nxc_captcha' )}:
			<div class="nxc-captcha-color-selector-disabled">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.noise_color.value}"></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div class="block">
		<label>{'Skip users'|i18n( 'extension/nxc_captcha' )}:</label>

		{def $user = false()}
		{foreach $content.skip_user_ids.value as $user_id}
			{set $user = fetch( 'content', 'object', hash( 'object_id', $user_id ) )}
			{if $user}
			{content_view_gui view=text_linked content_object=$user}{delimiter},{/delimiter}
			{/if}
		{/foreach}
		{if eq($user, false())}
		{'No selected users'|i18n( 'extension/nxc_captcha' )}
		{/if}
		{undef $user}
	</div>

	<div class="block">
		<label>{'Skip roles'|i18n( 'extension/nxc_captcha' )}:</label>

		{foreach $content.skip_role_ids.value as $role_id}
			{if is_set( $content.skip_role_ids.possible_values[ $role_id ] )}
			<a href="{concat( 'role/view/', $role_id )|ezurl( 'no' )}">{$content.skip_role_ids.possible_values[ $role_id ]}</a>{delimiter},{/delimiter}
			{/if}
		{/foreach}
		{if eq( is_set( $content.skip_role_ids.possible_values[ $role_id ] ), false())}
		{'No selected roles'|i18n( 'extension/nxc_captcha' )}
		{/if}
	</div>

	<div class="block">
		<label>{'Example'|i18n( 'extension/nxc_captcha' )}:</label>
		<img src="{concat( 'nxc_captcha/get/', $class_attribute.id, '/example/1' )|ezurl( 'no' )}" alt="{'Example'|i18n( 'extension/nxc_captcha' )}" />
	</div>
</div>
{undef $content}