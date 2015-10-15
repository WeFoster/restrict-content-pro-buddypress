<?php

RCPBP_Components::get_instance();
class RCPBP_Components {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the RCPBP_Components
	 *
	 * @return RCPBP_Components
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof RCPBP_Components ) {
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
		add_filter( 'bp_core_admin_tabs', array( $this, 'bp_restricted_tab' ) );
		add_action( bp_core_admin_hook(), array( $this, 'restricted_settings_page' ), 5 );
	}

	public function restricted_settings_page() {
		// Add the option pages
		$hooks[] = add_submenu_page(
			buddypress()->admin->settings_page,
			__( 'BuddyPress Restrictions', 'rcpbp' ),
			__( 'BuddyPress', 'buddypress' ),
			buddypress()->admin->capability,
			'rcpbp-restrictions',
			array( $this, 'settings_page_view' )
		);

		// Fudge the highlighted subnav item when on a BuddyPress admin page
		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'bp_core_modify_admin_menu_highlight' );
		}
	}

	public function bp_restricted_tab( $tabs ) {

		$tabs[] = array(
			'href' => bp_get_admin_url( add_query_arg( array( 'page' => 'rcpbp-restrictions' ), 'admin.php' ) ),
			'name' => __( 'Restrictions', 'rcpbp' )
		);

		return $tabs;
	}

	public function settings_page_view() {
		?>


		<div class="wrap">

			<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs( __( 'Restrictions', 'rcpbp' ) ); ?></h2>
			<form action="" method="post" id="bp-admin-page-form">

				<h1>Hi!</h1>

				<p class="submit clear">
					<input class="button-primary" type="submit" name="bp-admin-pages-submit" id="bp-admin-pages-submit" value="<?php esc_attr_e( 'Save Settings', 'buddypress' ) ?>"/>
				</p>

				<?php wp_nonce_field( 'bp-admin-restrictions' ); ?>

			</form>
		</div>

		<?php
	}

}