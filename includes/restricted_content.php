<?php

RCPBP_Restricted_Content::get_instance();
class RCPBP_Restricted_Content {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the RCPBP_Restricted_Content
	 *
	 * @return RCPBP_Restricted_Content
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof RCPBP_Restricted_Content ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'rcp_metabox_fields_after', array( $this, 'bp_fields' ) );
	}

	public function bp_fields( $rcp_meta_box ) {
		$rcp_prefix = 'rcp_';

		$member_types = apply_filters( 'rcpbp_restricted_member_types', bp_get_member_types( array(), 'objects' ) );

		if ( ! empty( $member_types ) ) {
			$this->member_type_field( $member_types );
		}

		if ( ! bp_is_active( 'groups' ) ) {
			return $rcp_meta_box;
		}

		$groups = apply_filters( 'rcpbp_restricted_groups', groups_get_groups( 'show_hidden=true' ) );

		if ( ! empty( $groups['groups'] ) ) {
			$this->groups_field( $groups['groups'] );
		}

		return $rcp_meta_box;

	}

	protected function member_type_field( $member_types ) {
		$field_id = 'rcp_member_types';
		?>
		<tr>
			<th style="width:20%" class="rcp_meta_box_label"><label for="<?php echo $field_id; ?>"><?php _e( 'Member Types', 'rcpbp' ); ?></label></th>
			<td class="rcp_meta_box_field">
				<?php foreach ( $member_types as $name => $member_type ) : ?>
					<input type="checkbox" value="<?php echo esc_attr( $name ); ?>" <?php // checked( true, in_array( $name, $selected ) ); ?> name="<?php echo $field_id; ?>[]" id="<?php echo $field_id; ?>_<?php echo esc_attr( $name ); ?>" />&nbsp;
					<label for="<?php echo $field_id; ?>_<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $member_type->labels['name'] ); ?></label><br/>
				<?php endforeach; ?>
			</td>
			<td class="rcp_meta_box_desc"><?php _e( 'Choose the BuddyPress Member Types that can see this post / page\'s content.', 'rcp' ); ?></td>
		</tr>
		<?php
	}


	protected function groups_field( $groups ) {
		$field_id = 'rcp_groups';
		?>
		<tr>
			<th style="width:20%" class="rcp_meta_box_label"><label for="<?php echo $field_id; ?>"><?php _e( 'Groups', 'rcpbp' ); ?></label></th>
			<td class="rcp_meta_box_field">
				<?php foreach ( $groups as $group ) : ?>
					<input type="checkbox" value="<?php echo absint( $group->id ); ?>" <?php // checked( true, in_array( $name, $selected ) ); ?> name="<?php echo $field_id; ?>[]" id="<?php echo $field_id; ?>_<?php echo absint( $group->id ); ?>" />&nbsp;
					<label for="<?php echo $field_id; ?>_<?php echo absint( $group->id ); ?>"><?php echo esc_html( $group->name ); ?></label><br/>
				<?php endforeach; ?>
			</td>
			<td class="rcp_meta_box_desc"><?php _e( 'Choose the BuddyPress Groups that can see this post / page\'s content. Any member of the selected group(s) will have access to this content.', 'rcp' ); ?></td>
		</tr>
	<?php
	}

}