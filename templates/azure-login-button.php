<?php
/**
 * Azure Login Button Template
 *
 * This file contains the HTML template for the Azure login button.
 *
 * @package login-azure
 * @author Sabith Ahammad
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

?>
<div>
	<div class="wal-wrapper">
		<div class="wal-spacearound">
			<a class="wal-button" href="<?php echo esc_attr( $url ); ?>">
				<div class="wal-logo">
					<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
						<title>MS-SymbolLockup</title>
						<rect x="1" y="1" width="9" height="9" fill="#f25022" />
						<rect x="1" y="11" width="9" height="9" fill="#00a4ef" />
						<rect x="11" y="1" width="9" height="9" fill="#7fba00" />
						<rect x="11" y="11" width="9" height="9" fill="#ffb900" />
					</svg>
				</div>
				<div class="wal-label">Sign in with Azure</div>
			</a>
		</div>
	</div>
</div>