<?php

namespace Wdk\Includes\Controls;

use Elementor\Control_Select2;

if (! defined('ABSPATH')) {
	exit();
}

class Wdk_Field_Selector extends Control_Select2
{

	const INIT = 'wdk-fields-selector';

	public function get_type()
	{
		return self::INIT;
	}


	public function content_template() {
		?>

		<#
			if(data.controlValue && data.controlValue.indexOf('__') !==-1){
			data.controlValue=data.controlValue.split('__')[1];
			}
		#>

		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php $this->print_control_uid(); ?>" class="elementor-select2 el-wdk-selector-field" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# if ( data.raw_options ) { #>
						<# _.each( data.raw_options, function( item ){ #>
							<option value="{{ _.escape(item.value) }}"
								<# if ( data.controlValue==item.value ) { #>
								selected="selected"
								<# } #>
									>{{{ _.escape(item.label) }}}
							</option>
						<# } ); #>
					<# } else { #>
						<# _.each( data.options, function( label, value ) { 

							if ( typeof data.controlValue == 'string' ) {
								var selected = ( value === data.controlValue ) ? 'selected' : '';
							} else if ( null !== data.controlValue ) {
								var selected = ( -1 !==  data.controlValue.indexOf( value ) ) ? 'selected' : '';
							}
							
							#>
							<option {{ selected }} value="{{ _.escape(value) }}">{{{ _.escape(label)}}}</option>
						<# } ); #>
					<# } #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
