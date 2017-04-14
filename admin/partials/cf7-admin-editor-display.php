<?php

/**
 * Provide a admin area view for the plugin to edit contact form 7 through the visual editor
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Cf7_Grid_Layout
 * @subpackage Cf7_Grid_Layout/admin/partials
 */
?>
<?php do_action( 'wpcf7_admin_warnings' ); ?>
<?php do_action( 'wpcf7_admin_notices' ); ?>
<input type="hidden" id="wpcf7-locale" name="wpcf7-locale" value="<?php echo esc_attr( $cf7_form->locale() ); ?>" />
<input type="hidden" id="active-tab" name="active-tab" value="<?php echo isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : '0'; ?>" />
<?php $nonce = wp_create_nonce( 'wpcf7-save-contact-form_' .  $post_id );?>

<div id="contact-form-editor">
<div class="keyboard-interaction"><?php echo sprintf( esc_html( __( '%s keys switch panels', 'contact-form-7' ) ), '<span class="dashicons dashicons-leftright"></span>' ); ?></div>

<?php

	$editor = new WPCF7_Editor( $cf7_form );
	$panels = array();

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$panels = array(
			'form-panel' => array(
				'title' => __( 'Form', 'contact-form-7' ),
				'callback' => array($this, 'grid_editor_panel') ),
			'mail-panel' => array(
				'title' => __( 'Mail', 'contact-form-7' ),
				'callback' => 'wpcf7_editor_panel_mail' ),
			'messages-panel' => array(
				'title' => __( 'Messages', 'contact-form-7' ),
				'callback' => 'wpcf7_editor_panel_messages' ) );

		$additional_settings = trim( $cf7_form->prop( 'additional_settings' ) );
		$additional_settings = explode( "\n", $additional_settings );
		$additional_settings = array_filter( $additional_settings );
		$additional_settings = count( $additional_settings );

		$panels['additional-settings-panel'] = array(
			'title' => $additional_settings
				? sprintf(
					__( 'Additional Settings (%d)', 'contact-form-7' ),
					$additional_settings )
				: __( 'Additional Settings', 'contact-form-7' ),
			'callback' => 'wpcf7_editor_panel_additional_settings' );
	}

	$panels = apply_filters( 'wpcf7_editor_panels', $panels );

	foreach ( $panels as $id => $panel ) {
		$editor->add_panel( $id, $panel['title'], $panel['callback'] );
	}

	$editor->display();
?>
</div><!-- #contact-form-editor -->

<form action="" class="dummy-form" data-id="dummy">
  <!-- DUMMY FORM to prevent some wp-core scripts from tempering with cf7 tags forms printed below-->
</form>
<?php

	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->print_panels( $cf7_form );

	do_action( 'wpcf7_admin_footer', $cf7_form );

  $dropdowns = get_option('_cf7sg_dynamic_dropdown_taxonomy',array());

?>
<script type="text/javascript">
(function( $ ) {
	'use strict';
  //hide the taxonomy metabox not used on this page.
  $(document).ready(function() {
    <?php
    $slugs = array();
    foreach($dropdowns as $id => $all_lists){
      foreach($all_lists as $slug => $taxonomy){
        if(isset($slugs[$slug])){
          continue;
        }else{
          $slugs[$slug] = $slug;
        }
        if( $taxonomy['hierarchical'] ){
          $hide_id = $slug.'div';
        }else{
          $hide_id = 'tagsdiv-'.$slug;
        }
        //debug_msg($taxonomy['slug']);
        if( $id != $post_id ){
          echo '$("#' . $hide_id . '").hide();';
        }else{
          echo '$("#' . $hide_id . '").show();';
        }
      }
    }
    ?>
  });
})( jQuery );
</script>