<?php
/** Server-rendered form feedback, private storage and administrator export. */
defined( 'ABSPATH' ) || exit();
add_action(
	'init',
	function () {
		register_post_type(
			'bl_submission',
			array(
				'label'               => __( 'Form Submissions', 'bottomline' ),
				'public'              => false,
				'show_ui'             => false,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'supports'            => array(),
				'map_meta_cap'        => false,
				'capabilities'        => array(
					'read_post'   => 'manage_options',
					'edit_post'   => 'manage_options',
					'delete_post' => 'manage_options',
				),
			)
		);
	}
);
function bl_form_columns() {
	return array(
		'firstName' => __( 'First name', 'bottomline' ),
		'lastName'  => __( 'Last name', 'bottomline' ),
		'email'     => __( 'Email', 'bottomline' ),
		'company'   => __( 'Company', 'bottomline' ),
		'phone'     => __( 'Phone', 'bottomline' ),
		'interest'  => __( 'Service', 'bottomline' ),
		'message'   => __( 'Message', 'bottomline' ),
	);
}
function bl_form_state() {
	return $GLOBALS['bl_form_state'] ?? array(
		'errors' => array(),
		'values' => array(),
	);
}
function bl_form_old( $key ) {
	return bl_form_state()['values'][ $key ] ?? '';
}
function bl_form_controls() {
	wp_nonce_field( 'bl_contact', 'bl_nonce' );
	echo '<input type="hidden" name="action" value="bl_contact"><input type="hidden" name="source_page" value="' .
		esc_attr( get_the_ID() ) .
		'">';
	echo '<div class="bl-honeypot" aria-hidden="true"><label>' .
		esc_html__( 'Leave this field empty', 'bottomline' ) .
		'<input name="website_confirm" tabindex="-1" autocomplete="off" type="text"></label></div>';
	$state = bl_form_state();
	if ( ! empty( $state['success'] ) ) {
		echo '<div class="form-success show" role="status">' .
			esc_html__( 'Thank you. Your message has been received. Our team will be in touch.', 'bottomline' ) .
			'</div>';
	}
	if ( $state['errors'] ) {
		echo '<div class="bl-form-errors" role="alert" tabindex="-1"><ul>';
		foreach ( $state['errors'] as $error ) {
			echo '<li>' . esc_html( $error ) . '</li>';
		}
		echo '</ul></div>';
	}
}
// A random HttpOnly cookie binds feedback to this browser; no personal data is placed in URLs.
add_action(
	'template_redirect',
	function () {
		if ( ! bl_uses_page_template( 'contact' ) ) {
			return;
		}
		nocache_headers();
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		$token =
		isset( $_COOKIE['bl_form_receipt'] ) && is_string( $_COOKIE['bl_form_receipt'] )
			? sanitize_key( wp_unslash( $_COOKIE['bl_form_receipt'] ) )
			: '';
		if ( preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
			$state = get_transient( 'bl_feedback_' . $token );
			if ( is_array( $state ) ) {
				$GLOBALS['bl_form_state'] = $state;
				delete_transient( 'bl_feedback_' . $token );
			}
			setcookie(
				'bl_form_receipt',
				'',
				array(
					'expires'  => time() - HOUR_IN_SECONDS,
					'path'     => '/',
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
	}
);
function bl_form_result( $page, $state ) {
	$token = bin2hex( random_bytes( 32 ) );
	set_transient( 'bl_feedback_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	setcookie(
		'bl_form_receipt',
		$token,
		array(
			'expires'  => time() + 10 * MINUTE_IN_SECONDS,
			'path'     => '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
	wp_safe_redirect( get_permalink( $page ) . '#contact-form', 303 );
	exit();
}
function bl_form_validate( $input ) {
	$values = array();
	$errors = array();
	foreach ( bl_form_columns() as $key => $label ) {
		$raw   = isset( $input[ $key ] ) && is_string( $input[ $key ] ) ? $input[ $key ] : '';
		$limit = $key === 'message' ? 10000 : 254;
		if ( mb_strlen( $raw ) > $limit ) {
			$errors[] = sprintf( __( '%s is too long.', 'bottomline' ), $label );
		}
		$values[ $key ] = $key === 'message' ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	foreach ( array( 'firstName', 'lastName', 'email', 'message' ) as $required ) {
		if ( $values[ $required ] === '' ) {
			$errors[] = sprintf( __( '%s is required.', 'bottomline' ), bl_form_columns()[ $required ] );
		}
	}
	if ( $values['email'] !== '' && ! is_email( $values['email'] ) ) {
		$errors[] = __( 'Enter a valid email address.', 'bottomline' );
	}
	$allowed = array_column( bl_rows( bl_field( 'form_services', 'option' ) ), 'label' );
	if ( $values['interest'] !== '' && ! in_array( $values['interest'], $allowed, true ) ) {
		$errors[] = __( 'Select one of the available services.', 'bottomline' );
	}
	return array(
		'values' => $values,
		'errors' => $errors,
	);
}
function bl_handle_contact() {
	if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'POST' ) {
		wp_die( esc_html__( 'Method not allowed.', 'bottomline' ), '', array( 'response' => 405 ) );
	}
	$input = wp_unslash( $_POST );
	$page  = isset( $input['source_page'] ) && is_scalar( $input['source_page'] ) ? absint( $input['source_page'] ) : 0;
	if ( get_post_status( $page ) !== 'publish' || ! bl_uses_page_template( 'contact', $page ) ) {
		wp_die( esc_html__( 'Invalid form source.', 'bottomline' ), '', array( 'response' => 400 ) );
	}
	$nonce = isset( $input['bl_nonce'] ) && is_string( $input['bl_nonce'] ) ? $input['bl_nonce'] : '';
	if ( ! wp_verify_nonce( $nonce, 'bl_contact' ) ) {
		bl_form_result(
			$page,
			array(
				'values' => array(),
				'errors' => array( __( 'Your form expired. Please try again.', 'bottomline' ) ),
			)
		);
	}
	if ( ! empty( $input['website_confirm'] ) ) {
		bl_form_result(
			$page,
			array(
				'values' => array(),
				'errors' => array( __( 'Unable to submit this message.', 'bottomline' ) ),
			)
		);
	}
	$state = bl_form_validate( $input );
	if ( $state['errors'] ) {
		bl_form_result( $page, $state );
	}
	$identity = hash_hmac( 'sha256', (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ), wp_salt( 'nonce' ) );
	$rate_key = 'bl_rate_' . $identity;
	if ( (int) get_transient( $rate_key ) >= 5 ) {
		$state['errors'][] = __( 'Too many messages. Please try again in 15 minutes.', 'bottomline' );
		bl_form_result( $page, $state );
	}
	// Short-lived lock and content digest prevent double clicks and repeated delivery.
	$lock = 'bl_form_lock_' . $identity;
	if ( ! add_option( $lock, time(), '', false ) ) {
		if ( (int) get_option( $lock ) < time() - 30 ) {
			delete_option( $lock );
		}
		$state['errors'][] = __( 'A submission is already processing. Please try again shortly.', 'bottomline' );
		bl_form_result( $page, $state );
	}
	$digest = 'bl_duplicate_' . hash_hmac( 'sha256', wp_json_encode( $state['values'] ), wp_salt( 'nonce' ) );
	if ( get_transient( $digest ) ) {
		delete_option( $lock );
		bl_form_result(
			$page,
			array(
				'values'  => array(),
				'errors'  => array(),
				'success' => true,
			)
		);
	}
	$id = wp_insert_post(
		array(
			'post_type'   => 'bl_submission',
			'post_status' => 'private',
			'post_title'  => __( 'Contact submission', 'bottomline' ),
			'meta_input'  => array(
				'_bl_form_type' => 'contact',
				'_bl_values'    => $state['values'],
			),
		),
		true,
	);
	if ( is_wp_error( $id ) || ! $id || get_post_meta( $id, '_bl_values', true ) !== $state['values'] ) {
		delete_option( $lock );
		$state['errors'][] = __( 'Your message could not be saved. Please try again.', 'bottomline' );
		bl_form_result( $page, $state );
	}
	set_transient( $digest, 1, 10 * MINUTE_IN_SECONDS );
	bl_notify_contact_submission( $id );
	set_transient( $rate_key, (int) get_transient( $rate_key ) + 1, 15 * MINUTE_IN_SECONDS );
	delete_option( $lock );
	bl_form_result(
		$page,
		array(
			'values'  => array(),
			'errors'  => array(),
			'success' => true,
		)
	);
}
add_action( 'admin_post_bl_contact', 'bl_handle_contact' );
add_action( 'admin_post_nopriv_bl_contact', 'bl_handle_contact' );
add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Form Submissions', 'bottomline' ),
			__( 'Form Submissions', 'bottomline' ),
			'manage_options',
			'bl-submissions',
			'bl_submissions_admin',
			'dashicons-feedback',
			26,
		);
	}
);
function bl_submissions_admin() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Access denied.', 'bottomline' ), '', array( 'response' => 403 ) );
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Form Submissions', 'bottomline' ) . '</h1>';
	$detail = isset( $_GET['submission'] ) && is_scalar( $_GET['submission'] ) ? absint( $_GET['submission'] ) : 0;
	if ( $detail ) {
		$post = get_post( $detail );
		if ( ! $post || $post->post_type !== 'bl_submission' || $post->post_status !== 'private' ) {
			wp_die( esc_html__( 'Submission not found.', 'bottomline' ), '', array( 'response' => 404 ) );
		}
		echo '<p><a href="' .
			esc_url( admin_url( 'admin.php?page=bl-submissions' ) ) .
			'">' .
			esc_html__( 'Back to submissions', 'bottomline' ) .
			'</a></p>';
		$rows                = array(
			__( 'Form type', 'bottomline' )    => get_post_meta( $detail, '_bl_form_type', true ),
			__( 'Submitted at', 'bottomline' ) => get_the_date( 'Y-m-d H:i:s', $post ),
		);
		$notification_labels = array(
			'accepted' => __( 'Accepted by the mail service', 'bottomline' ),
			'failed'   => __( 'Email could not be sent; submission is saved', 'bottomline' ),
			'disabled' => __( 'Notifications disabled or recipient invalid', 'bottomline' ),
			'sending'  => __( 'Email send started; result not recorded', 'bottomline' ),
		);
		$status              = get_post_meta( $detail, '_bl_notification_status', true );
		$rows[ __( 'Email notification', 'bottomline' ) ] = $notification_labels[ $status ] ?? __( 'Not recorded', 'bottomline' );
		$values = get_post_meta( $detail, '_bl_values', true );
		foreach ( bl_form_columns() as $key => $label ) {
			$rows[ $label ] = $values[ $key ] ?? '';
		}
		echo '<table class="widefat striped"><tbody>';
		foreach ( $rows as $label => $value ) {
			echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>' . bl_text( $value ) . '</td></tr>';
		}
		echo '</tbody></table></div>';
		return;
	}
	echo '<p><a class="button button-primary" href="' .
		esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bl_export' ), 'bl_export' ) ) .
		'">' .
		esc_html__( 'CSV Export', 'bottomline' ) .
		'</a></p>';
	$page  = isset( $_GET['paged'] ) && is_scalar( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$query = new WP_Query(
		array(
			'post_type'      => 'bl_submission',
			'post_status'    => 'private',
			'posts_per_page' => 25,
			'paged'          => $page,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		)
	);
	echo '<table class="widefat striped"><thead><tr>';
	foreach (
		array(
			__( 'Submission', 'bottomline' ),
			__( 'Form type', 'bottomline' ),
			__( 'Name', 'bottomline' ),
			__( 'Email', 'bottomline' ),
			__( 'Submitted at', 'bottomline' ),
		)
		as $label
	) {
		echo '<th>' . esc_html( $label ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( $query->posts as $post ) {
		$values = get_post_meta( $post->ID, '_bl_values', true );
		echo '<tr><td><a href="' .
			esc_url( add_query_arg( 'submission', $post->ID, admin_url( 'admin.php?page=bl-submissions' ) ) ) .
			'">#' .
			(int) $post->ID .
			'</a></td><td>' .
			esc_html( get_post_meta( $post->ID, '_bl_form_type', true ) ) .
			'</td><td>' .
			esc_html( ( $values['firstName'] ?? '' ) . ' ' . ( $values['lastName'] ?? '' ) ) .
			'</td><td>' .
			esc_html( $values['email'] ?? '' ) .
			'</td><td>' .
			esc_html( get_the_date( 'Y-m-d H:i:s', $post ) ) .
			'</td></tr>';
	}
	if ( ! $query->posts ) {
		echo '<tr><td colspan="5">' . esc_html__( 'No submissions yet.', 'bottomline' ) . '</td></tr>';
	}
	echo '</tbody></table><p>' .
		wp_kses_post(
			(string) paginate_links(
				array(
					'base'    => add_query_arg( 'paged', '%#%', admin_url( 'admin.php?page=bl-submissions' ) ),
					'format'  => '',
					'current' => $page,
					'total'   => $query->max_num_pages,
				)
			),
		) .
		'</p></div>';
}
function bl_csv_safe( $value ) {
	$value = is_scalar( $value ) ? (string) $value : '';
	// Block formulas even after whitespace, BOM or control characters, preserving quoted CSV content.
	if ( preg_match( '/^[\s\x00-\x20\x{FEFF}]*[=+@\-]/u', $value ) || preg_match( '/^[\t\r\n]/', $value ) ) {
		$value = "'" . $value;
	}
	return $value;
}
function bl_export_submissions() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Access denied.', 'bottomline' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'bl_export' );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="bottomline-submissions-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$stream = fopen( 'php://output', 'w' );
	fwrite( $stream, "\xEF\xBB\xBF" );
	fputcsv(
		$stream,
		array_merge(
			array( __( 'ID', 'bottomline' ), __( 'Form type', 'bottomline' ), __( 'Submitted at (site timezone)', 'bottomline' ) ),
			array_values( bl_form_columns() ),
		),
		',',
		'"',
		'',
	);
	$page = 1;
	do {
		$query = new WP_Query(
			array(
				'post_type'      => 'bl_submission',
				'post_status'    => 'private',
				'posts_per_page' => 250,
				'paged'          => $page++,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		foreach ( $query->posts as $post ) {
			$values = get_post_meta( $post->ID, '_bl_values', true );
			$row    = array( $post->ID, get_post_meta( $post->ID, '_bl_form_type', true ), $post->post_date );
			foreach ( bl_form_columns() as $key => $label ) {
				$row[] = bl_csv_safe( $values[ $key ] ?? '' );
			}
			fputcsv( $stream, $row, ',', '"', '' );
		}
	} while ( count( $query->posts ) === 250 );
	fclose( $stream );
	exit();
}
add_action( 'admin_post_bl_export', 'bl_export_submissions' );

/** Load client-side validation only on the contact page. */
function bl_enqueue_contact_validation() {
	if ( ! bl_uses_page_template( 'contact' ) ) {
		return;
	}
	$uri = get_template_directory_uri();
	wp_enqueue_script( 'bl-jquery-validate', $uri . '/assets/js/vendor/jquery.validate.js', array( 'jquery' ), '1.21.0', true );
	wp_enqueue_script( 'bl-contact-validation', $uri . '/assets/js/contact-validation.js', array( 'bl-jquery-validate' ), wp_get_theme( get_template() )->get( 'Version' ), true );
	wp_localize_script(
		'bl-contact-validation',
		'blContactValidation',
		array(
			'required'  => __( 'Please complete this field.', 'bottomline' ),
			'email'     => __( 'Enter a valid email address.', 'bottomline' ),
			/* translators: {0} is replaced by the maximum character count. */
			'maxlength' => __( 'Please enter no more than {0} characters.', 'bottomline' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bl_enqueue_contact_validation' );

/**
 * Email the configured recipient after a contact submission is stored.
 *
 * Notification failures do not discard the saved message or prompt a duplicate
 * submission. Sender settings apply only to this notification.
 *
 * @param int $submission_id Saved contact submission ID.
 */
function bl_notify_contact_submission( $submission_id ) {
	if ( get_post_type( $submission_id ) !== 'bl_submission' || get_post_meta( $submission_id, '_bl_notification_status', true ) ) {
		return;
	}
	$recipient = bl_field( 'form_notification_email', 'option' );
	if ( ! is_string( $recipient ) || ! is_email( $recipient ) ) {
		update_post_meta( $submission_id, '_bl_notification_status', 'disabled' );
		return;
	}
	$values = get_post_meta( $submission_id, '_bl_values', true );
	if ( ! is_array( $values ) ) {
		return;
	}
	/* translators: %d is the stored submission ID. */
	$subject = sprintf( __( 'New contact submission #%d', 'bottomline' ), $submission_id );
	$body    = bl_contact_notification_html( $submission_id, $values );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( is_email( $values['email'] ?? '' ) ) {
		$headers[] = 'Reply-To: ' . $values['email'];
	}
	update_post_meta( $submission_id, '_bl_notification_status', 'sending' );
	$sender_name  = sanitize_text_field( (string) bl_field( 'form_sender_name', 'option' ) );
	$sender_email = bl_field( 'form_sender_email', 'option' );
	$from_name    = static function ( $default ) use ( $sender_name ) {
		return $sender_name !== '' ? $sender_name : $default;
	};
	$from_email   = static function ( $default ) use ( $sender_email ) {
		return is_string( $sender_email ) && is_email( $sender_email ) ? $sender_email : $default;
	};
	add_filter( 'wp_mail_from_name', $from_name );
	add_filter( 'wp_mail_from', $from_email );
	try {
		$accepted = wp_mail( $recipient, $subject, $body, $headers );
	} finally {
		remove_filter( 'wp_mail_from_name', $from_name );
		remove_filter( 'wp_mail_from', $from_email );
	}
	update_post_meta( $submission_id, '_bl_notification_status', $accepted ? 'accepted' : 'failed' );
}

/** Build an HTML notification with escaped submission values. */
function bl_contact_notification_html( $submission_id, $values ) {
	ob_start();
	require get_template_directory() . '/emails/contact-notification.php';
	return ob_get_clean();
}
