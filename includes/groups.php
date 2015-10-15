<?php

RCPBP_Groups::get_instance();
class RCPBP_Groups {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the RCPBP_Groups
	 *
	 * @return RCPBP_Groups
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof RCPBP_Groups ) {
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
		add_filter( 'bp_active_components', array( $this, 'active_components' ) );
	}

	public function active_components( $active ) {
		unset( $active['groups'] );
		return $active;
	}

}