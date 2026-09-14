<?php
/** Test notification behavior without sending external email. */
require ( getenv( 'BL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/.test-wordpress' ) . '/wp-load.php';
function check_notification( $ok, $label ) {
	if ( ! $ok ) {
		fwrite( STDERR, 'FAIL ' . $label . PHP_EOL );
		exit( 1 );
	}
	echo 'PASS ' . $label . PHP_EOL;
}
$original = bl_field( 'form_notification_email', 'option' );
$mails = array();
$accept = true;
$mock = function ( $pre, $atts ) use ( &$mails, &$accept ) {
	$mails[] = $atts;
	return $accept;
};
add_filter( 'pre_wp_mail', $mock, 10, 2 );
$ids = array();
$values = array( 'firstName' => 'Local', 'lastName' => 'Test', 'email' => 'visitor@example.test', 'message' => "Test enquiry\nSecond line" );
try {
	foreach ( array( '', 'invalid', 'recipient@example.test', 'recipient@example.test' ) as $index => $recipient ) {
		update_field( 'field_bl_form_notification_email', $recipient, 'option' );
		$id = wp_insert_post( array( 'post_type' => 'bl_submission', 'post_status' => 'private', 'post_title' => 'Notification test', 'meta_input' => array( '_bl_values' => $values ) ) );
		$ids[] = $id;
		$accept = $index !== 3;
		$before = count( $mails );
		bl_notify_contact_submission( $id );
		$status = get_post_meta( $id, '_bl_notification_status', true );
		if ( $index < 2 ) {
			check_notification( count( $mails ) === $before && $status === 'disabled', 'blank or invalid recipient sends no email' );
		} else {
			$mail = end( $mails );
			check_notification( $mail['to'] === $recipient && str_contains( $mail['message'], $values['message'] ), 'configured recipient receives submission details' );
			check_notification( in_array( 'Reply-To: visitor@example.test', $mail['headers'], true ), 'reply address uses validated visitor email' );
			check_notification( $status === ( $accept ? 'accepted' : 'failed' ), 'mail result recorded accurately' );
			check_notification( get_post_meta( $id, '_bl_values', true ) === $values, 'saved entry survives mail result' );
			$count = count( $mails );
			bl_notify_contact_submission( $id );
			check_notification( count( $mails ) === $count, 'same entry does not notify twice' );
		}
	}
} finally {
	update_field( 'field_bl_form_notification_email', $original ?: '', 'option' );
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	remove_filter( 'pre_wp_mail', $mock, 10 );
}
echo 'Notification checks complete; no email sent.' . PHP_EOL;
