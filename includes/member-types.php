<?php

RCPBP_Member_Types::get_instance();
class RCPBP_Member_Types {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the RCPBP_Member_Types
	 *
	 * @return RCPBP_Member_Types
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof RCPBP_Member_Types ) {
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

	/**
	 * Actions and Filters
	 */
	protected function hooks() {
		add_action( 'rcp_add_subscription_form',   array( $this, 'subscription_member_type' ) );
		add_action( 'rcp_edit_subscription_form',  array( $this, 'subscription_member_type' ) );

		add_action( 'rcp_edit_subscription_level', array( $this, 'subscription_member_type_save' ), 10, 2 );
		add_action( 'rcp_add_subscription',        array( $this, 'subscription_member_type_save' ), 10, 2 );

		add_action( 'update_user_metadata',        array( $this, 'set_user_member_type' ), 10, 4 );
	}

	public function subscription_member_type( $level = null ) {
		if ( ! $types = bp_get_member_types( array(), 'all' ) ) {
			return;
		}

		$current_type = ( empty( $level->id ) ) ? '' : rcpbp_get_subscription_member_type( $level->id ); ?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="rcp-role"><?php _e( 'Member Type', 'rcpbp' ); ?></label>
			</th>
			<td>
				<select name="member-type" id="rcp-member-type">
					<option value=""><?php _e( 'None', 'rcpbp' ); ?></option>
					<?php foreach( $types as $type ) : ?>
						<option value="<?php echo $type->name; ?>" <?php selected( $current_type, $type->name ); ?>><?php echo $type->labels['name']; ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php _e( 'The BuddyPress member type given to the member after signing up.', 'rcpbp' ); ?></p>
			</td>
		</tr>

	<?php
	}

	/**
	 * Save the member type for this subscription
	 *
	 * @param $subscription_id
	 * @param $args
	 */
	public function subscription_member_type_save( $subscription_id, $args ) {

		// make sure the member type is set
		if ( empty( $_POST['member-type'] ) ) {
			return;
		}

		// make sure the member type is valid
		if ( ! in_array( $_POST['member-type'], (array) bp_get_member_types( array(), 'names' ) ) ) {
			return;
		}

		rcpbp_set_subscription_member_type( $subscription_id, $_POST['member-type'] );
	}

	/**
	 * Update the member type any time the subscription level is changed
	 *
	 * @param $return
	 * @param $user_id
	 * @param $meta_key
	 * @param $subscription_id
	 *
	 * @return mixed
	 */
	public function set_user_member_type( $return, $user_id, $meta_key, $subscription_id ) {

		// we are only interested in the subscription level meta key
		if ( 'rcp_subscription_level' != $meta_key ) {
			return $return;
		}

		// if the subscription matches the current subscription, cancel
		if ( rcp_get_subscription_id( $user_id ) == $subscription_id ) {
			return $return;
		}

		// set the member type. Overwrites the last value even if the new subscription does not have a member type
		bp_set_member_type( $user_id, rcpbp_get_subscription_member_type( $subscription_id ) );

		do_action( 'rcpbp_set_user_member_type', $user_id, $subscription_id );

		return $return;
	}

}

/**
 * Get subscription member type map
 *
 * @return mixed|void
 */
function rcpbp_get_subscription_member_type_map() {
	return apply_filters( 'rcpbp_get_subscription_member_type_map', get_option( 'rcpbp_subscription_member_types', array() ) );
}

/**
 * Get the member type for this subscription
 *
 * @param $subscription_id
 *
 * @return string
 */
function rcpbp_get_subscription_member_type( $subscription_id ) {
	$map = rcpbp_get_subscription_member_type_map();

	// Does this subscription have a member type assigned?
	if ( empty( $map[ $subscription_id ] ) ) {
		return '';
	}

	$member_type = $map[ $subscription_id ];

	// make sure the member type is valid
	if ( ! in_array( $member_type, (array) bp_get_member_types( array(), 'names' ) ) ) {
		return '';
	}

	return $member_type;
}

/**
 * Set the member type for this subscription
 *
 * @param $subscription_id
 * @param $member_type
 */
function rcpbp_set_subscription_member_type( $subscription_id, $member_type ) {
	$map = rcpbp_get_subscription_member_type_map();
	$map[ $subscription_id ] = $member_type;
	update_option( 'rcpbp_subscription_member_types', $map );

	do_action( 'rcpbp_set_subscription_member_type', $subscription_id );
}

/**
 * Remove member type from subscription
 *
 * @param $subscription_id
 */
function rcpbp_remove_subscription_member_type( $subscription_id ) {
	$map = rcpbp_get_subscription_member_type_map();
	unset( $map[ $subscription_id ] );
	update_option( 'rcpbp_subscription_member_types', $map );

	do_action( 'rcpbp_remove_subscription_member_type', $subscription_id );
}
add_action( 'rcp_remove_subscription_level', 'rcpbp_remove_subscription_member_type' );