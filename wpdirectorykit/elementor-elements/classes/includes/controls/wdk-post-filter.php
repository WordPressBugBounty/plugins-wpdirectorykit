<?php
/**
 * Class: Wdk_Post_Filter
 * Name:  Wdk Post Filter
 * Slug:  wdk-post-filter
 */

namespace Wdk\Includes\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Wdk Post Filter extended from select2
 */
class Wdk_Post_Filter extends Control_Select2 {

	const TYPE = 'wdk-post-filter';

	/**
	 * Returns the type of the control
	 */
	public function get_type() {
		return self::TYPE;
	}
}
