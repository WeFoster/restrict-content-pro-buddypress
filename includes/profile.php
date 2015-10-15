<?php

RCPBP_Profile::get_instance();
class RCPBP_Profile {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the RCPBP_Profile
	 *
	 * @return RCPBP_Profile
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof RCPBP_Profile ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		$this->hooks();
	}

	protected function hooks() {
		bp_is_active();
		add_action( 'bp_settings_setup_nav', array( $this, 'setup_settings_nav' ) );
	}

	/**
	 * Set up the Settings > Account nav item.
	 */
	public function setup_settings_nav() {
		if ( ! bp_is_active( 'settings' ) ) {
			return;
		}

		// Determine user to use
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		// Get the settings slug
		$settings_slug = bp_get_settings_slug();

		bp_core_new_subnav_item( array(
			'name'            => _x( 'Account', 'Account settings sub nav', 'rcpbp' ),
			'slug'            => 'account',
			'parent_url'      => trailingslashit( $user_domain . $settings_slug ),
			'parent_slug'     => $settings_slug,
			'screen_function' => array( $this, 'profile_account_screen' ),
			'position'        => 40,
			'user_has_access' => bp_core_can_edit_settings()
		) );

		add_filter( 'bp_get_template_stack', array( $this, 'template_stack' ) );

	}

	public function profile_account_screen() {

		// Make sure we are on the correct page
		if ( bp_action_variables() || 'account' != bp_current_action() ) {
			bp_do_404();
			return;
		}

		bp_core_load_template( 'members/single/settings/account' );


		add_filter( 'bp_get_template_part', array( $this, 'account_template_part' ), 10, 3 );

	}

	public function account_template_part( $templates, $slug, $name ) {
		if ( $slug != 'members/single/plugins' ) {
			return $templates;
		}

		return array( 'members/single/settings/account.php' );
	}

	public function template_stack( $templates ) {
		$templates[] = RCPBP_PATH . 'templates/buddypress';
		return $templates;
	}
}