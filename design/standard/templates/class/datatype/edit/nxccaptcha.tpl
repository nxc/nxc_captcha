{ezcss_require(
	array(
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css',
		'colorpicker.css',
		'nxc.captcha.css'
	)
)}
{ezscript_require(
	array(
		'ezjsc::jquery',
		'jquery-ui-1.8.16.custom.min.js',
		'nxc.captcha.js',
		'colorpicker/eye.js',
		'colorpicker/utils.js',
		'colorpicker/layout.js',
		'colorpicker/colorpicker.js'
	)
)}

{def $content = $class_attribute.content}

<div class="break"></div>
<div class="block">
	<fieldset>
		<legend>{'Properies'|i18n( 'extension/nxc_captcha' )}</legend>

		<div class="nxc-captcha-property-block">
			<label>{'Length (characters)'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[length]" value="{$content.length.value}" class="nxc-captcha-slider-input" />
			<div class="nxc-captcha-tip">{'from %min to %max characters'|i18n( 'extension/nxc_captcha', null, hash( '%min', $content.length.min, '%max', $content.length.max ) )}</div>
			<div class="nxc-captcha-slider">
				<input type="hidden" class="nxc-captcha-slider-min" value="{$content.length.min}" />
				<input type="hidden" class="nxc-captcha-slider-max" value="{$content.length.max}" />
			</div>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Width'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[width]" value="{$content.width.value}" class="nxc-captcha-slider-input" />
			<div class="nxc-captcha-tip">{'from %min to %max pixels'|i18n( 'extension/nxc_captcha', null, hash( '%min', $content.width.min, '%max', $content.width.max ) )}</div>
			<div class="nxc-captcha-slider">
				<input type="hidden" class="nxc-captcha-slider-min" value="{$content.width.min}" />
				<input type="hidden" class="nxc-captcha-slider-max" value="{$content.width.max}" />
			</div>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Height'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[height]" value="{$content.height.value}" class="nxc-captcha-slider-input" />
			<div class="nxc-captcha-tip">{'from %min to %max pixels'|i18n( 'extension/nxc_captcha', null, hash( '%min', $content.height.min, '%max', $content.height.max ) )}</div>
			<div class="nxc-captcha-slider">
				<input type="hidden" class="nxc-captcha-slider-min" value="{$content.height.min}" />
				<input type="hidden" class="nxc-captcha-slider-max" value="{$content.height.max}" />
			</div>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Noise level'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[noise_level]" value="{$content.noise_level.value}" class="nxc-captcha-slider-input" />
			<div class="nxc-captcha-tip">{'from %min to %max'|i18n( 'extension/nxc_captcha', null, hash( '%min', $content.noise_level.min, '%max', $content.noise_level.max ) )}</div>
			<div class="nxc-captcha-slider">
				<input type="hidden" class="nxc-captcha-slider-min" value="{$content.noise_level.min}" />
				<input type="hidden" class="nxc-captcha-slider-max" value="{$content.noise_level.max}" />
			</div>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Max text tilt angle'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[text_tilt_angle]" value="{$content.text_tilt_angle.value}" class="nxc-captcha-slider-input" />
			<div class="nxc-captcha-tip">{'from %min to %max'|i18n( 'extension/nxc_captcha', null, hash( '%min', $content.text_tilt_angle.min, '%max', $content.text_tilt_angle.max ) )}</div>
			<div class="nxc-captcha-slider">
				<input type="hidden" class="nxc-captcha-slider-min" value="{$content.text_tilt_angle.min}" />
				<input type="hidden" class="nxc-captcha-slider-max" value="{$content.text_tilt_angle.max}" />
			</div>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Type (characters)'|i18n( 'extension/nxc_captcha' )}:</label>
			<select name="nxc_captcha_{$class_attribute.id}_options[type]">
				{foreach $content.type.possible_values as $type_id => $type}
				<option value="{$type_id}" {if eq( $type_id, $content.type.value )}selected="selected"{/if}>{$type}</option>
				{/foreach}
			</select>
		</div>

		<div class="nxc-captcha-property-block">
			<label>{'Exclude characters (separated by comma)'|i18n( 'extension/nxc_captcha' )}:</label>
			<div>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[exclude_characters]" value="{$content.exclude_characters.value}" size="64" />
			</div>
		</div>

	</fieldset>
</div>

<div class="block">
	<fieldset>
		<legend>{'Colors'|i18n( 'extension/nxc_captcha' )}</legend>

		<div class="element nxc-captcha-color-block">
			<label>{'Characters'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[characters_color]" value="{$content.characters_color.value}" class="nxc-captcha-color-selector-input" />
			<div class="nxc-captcha-color-selector">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.characters_color.value}"></div>
			</div>
			<div class="break"></div>
			<div class="nxc-captcha-tip">{'Example: 2a52df'|i18n( 'extension/nxc_captcha' )}</div>
		</div>

		<div class="element nxc-captcha-color-block">
			<label>{'Background'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[background_color]" value="{$content.background_color.value}" class="nxc-captcha-color-selector-input" />
			<div class="nxc-captcha-color-selector">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.background_color.value}"></div>
			</div>
			<div class="break"></div>
			<div class="nxc-captcha-tip">{'Example: 2a55ff'|i18n( 'extension/nxc_captcha' )}</div>
		</div>

		<div class="element nxc-captcha-color-block">
			<label>{'Noise'|i18n( 'extension/nxc_captcha' )}:</label>
			<input type="text" name="nxc_captcha_{$class_attribute.id}_options[noise_color]" value="{$content.noise_color.value}" class="nxc-captcha-color-selector-input" />
			<div class="nxc-captcha-color-selector">
				<div class="nxc-captcha-color-selector-bg" style="background-color: #{$content.noise_color.value}"></div>
			</div>
			<div class="break"></div>
			<div class="nxc-captcha-tip">{'Example: ddef12'|i18n( 'extension/nxc_captcha' )}</div>
		</div>

	</fieldset>
</div>

<div class="block">
	<fieldset>

		<legend>{'Skip users'|i18n( 'extension/nxc_captcha' )}</legend>
		<p>{'Users, for whom captcha will be skipped'|i18n( 'extension/nxc_captcha' )}</p>

		<table>
			<tbody>

				{def $user = false()}
				{foreach $content.skip_user_ids.value as $user_id}
					{set $user = fetch( 'content', 'object', hash( 'object_id', $user_id ) )}
					{if $user}
					<tr>
						<td>
							{content_view_gui view=text_linked content_object=$user}
						</td>
						<td>
							<input type="hidden" name="nxc_captcha_{$class_attribute.id}_options[skip_user_ids][]" value="{$user_id}" />
							<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_remove_skip_user_{$user_id}]" value="{'Remove'|i18n( 'extension/nxc_captcha' )}" />
						</td>
					</tr>
					{/if}
				{/foreach}
				{undef $user}

			</tbody>
		</table>

		<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_browse_user]" value="{'Browse'|i18n( 'extension/nxc_captcha' )}" />

	</fieldset>
</div>

<div class="block">
	<fieldset>

		<legend>{'Skip roles'|i18n( 'extension/nxc_captcha' )}</legend>
		<select name="nxc_captcha_{$class_attribute.id}_options[skip_role_ids][]" multiple="multiple" size="10">
		{foreach $content.skip_role_ids.possible_values as $role_id => $role}
		<option value="{$role_id}" {if $content.skip_role_ids.value|contains( $role_id )}selected="selected"{/if}>{$role}</option>
		{/foreach}
		</select>

	</fieldset>
</div>

{undef $content}