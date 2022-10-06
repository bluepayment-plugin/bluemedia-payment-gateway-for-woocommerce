<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Woocommerce;

use Exception;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Checkbox_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Decimal_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Number_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Select_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Text_Area_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Field_Text_Interface;

class Field {

	/**
	 * @throws Exception
	 */
	public function get_html( Field_Interface $field ): string {
		if ( $field instanceof Field_Checkbox_Interface ) {
			return $this->get_checkbox( $field );
		}

		if ( $field instanceof Field_Number_Interface ) {
			return $this->get_number( $field );
		}

		if ( $field instanceof Field_Select_Interface ) {
			return $this->get_select( $field );
		}

		if ( $field instanceof Field_Text_Interface ) {
			return $this->get_text( $field );
		}

		if ( $field instanceof Field_Text_Area_Interface ) {
			return $this->get_text_area( $field );
		}

		throw new Exception( 'Unknown field type' );
	}

	/**
	 * @param Field_Text_Area_Interface | Field_Interface $field_text_Area_Interface
	 *
	 * @return string
	 */
	public function get_text_area( Field_Text_Area_Interface $field_text_Area_Interface ): string {

		ob_start();
		woocommerce_wp_textarea_input(
			[
				'id'    => $field_text_Area_Interface->get_name(),
				'name'  => $field_text_Area_Interface->get_name(),
				'value' => $field_text_Area_Interface->get_value(),
			]
		);

		return ob_get_clean();
	}

	/**
	 * @param Field_Text_Interface | Field_Interface $field_text_Interface
	 *
	 * @return string
	 */
	public function get_text( Field_Text_Interface $field_text_Interface ): string {

		ob_start();
		woocommerce_wp_text_input(
			[
				'id'    => $field_text_Interface->get_name(),
				'name'  => $field_text_Interface->get_name(),
				'value' => $field_text_Interface->get_value(),
			]
		);

		return ob_get_clean();
	}


	/**
	 * @param Field_Select_Interface | Field_Interface $field_select_Interface
	 *
	 * @return string
	 */
	public function get_select( Field_Select_Interface $field_select_Interface ): string {

		ob_start();
		woocommerce_wp_select(
			[
				'id'      => $field_select_Interface->get_name(),
				'name'    => $field_select_Interface->get_name(),
				'value'   => $field_select_Interface->get_value(),
				'options' => $field_select_Interface->get_options(),
			]
		);

		return ob_get_clean();
	}

	/**
	 * @param Field_Checkbox_Interface | Field_Interface $checkbox
	 *
	 * @return string
	 */
	public function get_checkbox( Field_Checkbox_Interface $checkbox ): string {

		ob_start();
		woocommerce_wp_checkbox(
			[
				'name'  => $checkbox->get_name(),
				'value' => $checkbox->get_value(),
			]
		);

		return ob_get_clean();
	}

	/**
	 * @param Field_Number_Interface | Field_Interface $field_number_Interface
	 *
	 * @return string
	 */
	public function get_number( Field_Number_Interface $field_number_Interface ): string {

		ob_start();
		woocommerce_wp_text_input(
			[
				'name'  => $field_number_Interface->get_name(),
				'value' => $field_number_Interface->get_value(),
				'type'  => 'number',
			]
		);

		return ob_get_clean();
	}


}
