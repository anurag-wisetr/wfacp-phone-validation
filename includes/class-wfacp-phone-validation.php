<?php

class WFACP_Phone_Validation {

	private $countries_phone_regex = [
		'af' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Afghanistan (‫افغانستان‬‎)', 'code' => '93' ],
		'al' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Albania (Shqipëri)', 'code' => '355' ],
		'bg' => [ 'pattern' => '/^((\+)\d{13}){1}?$/', 'name' => 'Bulgaria (България)', 'code' => '359' ],
		'br' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Brazil (Brasil)', 'code' => '55' ],
		'ca' => [
			'pattern'   => '/^((\+)\d{11}){1}?$/',
			'name'      => 'Canada',
			'code'      => '1',
			"areaCodes" => [
				"204",
				"226",
				"236",
				"249",
				"250",
				"289",
				"306",
				"343",
				"365",
				"387",
				"403",
				"416",
				"418",
				"431",
				"437",
				"438",
				"450",
				"506",
				"514",
				"519",
				"548",
				"579",
				"581",
				"587",
				"604",
				"613",
				"639",
				"647",
				"672",
				"705",
				"709",
				"742",
				"778",
				"780",
				"782",
				"807",
				"819",
				"825",
				"867",
				"873",
				"902",
				"905"
			]
		],
		'hk' => [ 'pattern' => '/^((\+)\d{13}){1}?$/', 'name' => 'Hong Kong (香港)', 'code' => '852' ],
		'il' => [ 'pattern' => '/^((\+)\d{13}){1}?$/', 'name' => 'israel', 'code' => '972' ],
		'in' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'India (भारत)', 'code' => '91' ],
		'it' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Italy (Italia)', 'code' => '39' ],
		'cn' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'China (中国)', 'code' => '86' ],
		'jp' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Japan (日本)', 'code' => '81' ],
		'ae' => [ 'pattern' => '/^((\+)\d{13}){1}?$/', 'name' => 'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)', 'code' => '971' ],
		'gb' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'United Kingdom', 'code' => '44' ],
		'nl' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Netherlands (Nederland)', 'code' => '31' ],
		'fr' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'France', 'code' => '33' ],
		'vn' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Vietnam (Việt Nam)', 'code' => '84' ],
		'si' => [ 'pattern' => '/^((\+)\d{13}){1}?$/', 'name' => 'Slovenia (Slovenija)', 'code' => '386' ],
		'es' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Spain (España)', 'code' => '34' ],
		'ro' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Romania (România)', 'code' => '40' ],
		'mx' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Mexico (México)', 'code' => '52' ],
		'pk' => [ 'pattern' => '/^((\+)\d{12}){1}?$/', 'name' => 'Pakistan (‫پاکستان‬‎)', 'code' => '92' ],
		'us' => [ 'pattern' => '/^((\+)\d{11}){1}?$/', 'name' => 'United States', 'code' => '1' ],
	];

	public function __construct() {
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'wfacp_phone_validation' ], 10, 2 );
		add_action( 'wfacp_after_checkout_page_found', function () {
			add_action( 'wp_enqueue_scripts', [ $this, 'wfacp_load_scripts' ] );
		} );
		add_action( 'wfacp_before_billing_phone_field', [ $this, 'flag_dropdown_list' ], 10, 3 );
	}

	function wfacp_load_scripts() {
		wp_enqueue_style( 'flag_style', plugin_dir_url( __DIR__ ) . 'assets/css/display-flag.css' );
		wp_enqueue_style( 'custom_style', plugin_dir_url( __DIR__ ) . 'assets/css/custom.css' );
		wp_enqueue_script( 'phone_mask_script', plugin_dir_url( __DIR__ ) . 'assets/js/jquery.mask.js', [ 'jquery' ], '1.0.0', true );
		wp_enqueue_script( 'phone_validation_script', plugin_dir_url( __DIR__ ) . 'assets/js/phone-validation.js', [ 'jquery' ], '1.0.0', true );

		$regexes = apply_filters( 'wfacp_phone_validation_regexes', $this->countries_phone_regex );
		wp_localize_script( 'phone_validation_script', 'phone_data', $regexes );
	}

	function wfacp_phone_validation( $fields, $error ) {
		$country = strtolower( $fields['billing_country'] );
		if ( isset( $fields['billing_country'] ) && array_key_exists( $country, $this->countries_phone_regex ) ) {
			if ( ! preg_match( $this->countries_phone_regex[ $country ]['pattern'], $fields['billing_phone'] ) ) {
				$error->add( 'validation', 'Phone number is not valid.' );
			}
		}
	}

	function flag_dropdown_list( $key, $field, $field_value ) {
		?>
        <div class="wfacp-flag-list">
            <ul class="iti__country-list" id="country-listbox" aria-expanded="true" role="listbox">
				<?php
				foreach ( $this->countries_phone_regex as $country => $data ) {
					?>
                    <li class="iti__country iti__preferred" tabindex="-1" id="iti-item-<?php echo $country; ?>" role="option" data-dial-code="<?php echo $data['code']; ?>" data-country="<?php echo $country; ?>" data-country-name="<?php echo $data['name']; ?>">
                        <div class="iti__flag-box">
                            <div class="iti__flag iti__<?php echo $country; ?>"></div>
                        </div>
                        <span class="iti__country-name"><?php echo $data['name']; ?></span>
                        <span class="iti__dial-code">+<?php echo $data['code']; ?></span>
                    </li>
					<?php
				}
				?>
            </ul>
        </div>
		<?php
	}
}