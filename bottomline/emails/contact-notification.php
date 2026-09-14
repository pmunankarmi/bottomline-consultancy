<?php
/**
 * Styled contact notification email.
 *
 * @package Bottomline
 * @var int   $submission_id Saved submission ID.
 * @var array $values        Validated form values.
 */
$site_name   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
$details_url = admin_url( 'admin.php?page=bl-submissions&submission=' . $submission_id );
?>
<!doctype html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php esc_html_e( 'New contact enquiry', 'bottomline' ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f0f5f3;color:#243b33;font-family:Arial,Helvetica,sans-serif;">
	<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f0f5f3;">
		<tr>
			<td align="center" style="padding:32px 12px;">
				<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background-color:#ffffff;border:1px solid #dbe7e1;">
					<tr>
						<td style="padding:28px;background-color:#00553a;color:#ffffff;">
							<p style="margin:0 0 14px;font-size:13px;letter-spacing:1px;"><?php echo esc_html( $site_name ); ?></p>
							<h1 style="margin:0;font-size:26px;line-height:1.3;"><?php esc_html_e( 'New contact enquiry', 'bottomline' ); ?></h1>
							<p style="margin:12px 0 0;font-size:13px;color:#d4eee3;">
								<?php echo esc_html( sprintf( __( 'Submission #%d', 'bottomline' ), $submission_id ) ); ?>
								&middot; <?php echo esc_html( get_the_date( 'F j, Y, g:i a', $submission_id ) ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<td style="padding:28px;">
							<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;line-height:1.6;table-layout:fixed;">
								<?php foreach ( bl_form_columns() as $key => $label ) : ?>
									<?php
									if ( $key === 'message' ) {
										continue;
									}
									?>
									<tr>
										<th scope="row" align="left" width="32%" style="padding:12px 8px 12px 0;border-bottom:1px solid #e4ece8;vertical-align:top;color:#527064;font-weight:normal;"><?php echo esc_html( $label ); ?></th>
										<td style="padding:12px 0;border-bottom:1px solid #e4ece8;word-wrap:break-word;overflow-wrap:anywhere;"><?php echo esc_html( ( $values[ $key ] ?? '' ) !== '' ? $values[ $key ] : '—' ); ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
							<h2 style="margin:26px 0 12px;font-size:17px;"><?php esc_html_e( 'Message', 'bottomline' ); ?></h2>
							<div style="padding:18px;background-color:#f2f7f4;border-left:3px solid #009a66;font-size:15px;line-height:1.7;word-wrap:break-word;overflow-wrap:anywhere;">
								<?php echo nl2br( esc_html( $values['message'] ?? '' ) ); ?>
							</div>
							<p style="margin:28px 0 0;">
								<a href="<?php echo esc_url( $details_url ); ?>" style="display:inline-block;padding:14px 22px;background-color:#007a52;border:1px solid #007a52;color:#ffffff;text-decoration:none;font-size:14px;font-weight:bold;"><?php esc_html_e( 'View saved submission', 'bottomline' ); ?></a>
							</p>
							<p style="margin:16px 0 0;color:#527064;font-size:12px;line-height:1.6;"><?php esc_html_e( 'Reply to this email to contact the visitor. Sign in to WordPress to view the saved submission.', 'bottomline' ); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
