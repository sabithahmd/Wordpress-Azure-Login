<?php
/**
 * Azure Login Button Template
 *
 * This file contains the HTML template for the Azure login button.
 *
 * @package azure-login
 * @author Sabith Ahammad
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

?>
<div>
	<style>
		.wal-wrapper {
				box-sizing: border-box;
				display: block;
				width: 100%;
				padding: 12px 12px 24px 12px;
				text-align: center;
		}

		.wal-spacearound {
				display: inline-block;
		}

		.wal-wrapper form {
				display: none;
		}

		.wal-button {
				text-decoration: none;
				border: 1px solid #8c8c8c;
				background: #ffffff;
				display: flex;
				display: -webkit-box;
				display: -moz-box;
				display: -webkit-flex;
				display: -ms-flexbox;
				-webkit-box-align: center;
				-moz-box-align: center;
				-ms-flex-align: center;
				-webkit-align-items: center;
				align-items: center;
				-webkit-box-pack: center;
				-moz-box-pack: center;
				-ms-flex-pack: center;
				-webkit-justify-content: center;
				justify-content: center;
				cursor: pointer;
				max-height: 41px;
				min-height: 41px;
				height: 41px;
		}

		.wal-logo {
				padding-left: 12px;
				padding-right: 6px;
				-webkit-flex-shrink: 1;
				-moz-flex-shrink: 1;
				flex-shrink: 1;
				width: 21px;
				height: 21px;
				box-sizing: content-box;
				display: flex;
				display: -webkit-box;
				display: -moz-box;
				display: -webkit-flex;
				display: -ms-flexbox;
				-webkit-box-pack: center;
				-moz-box-pack: center;
				-ms-flex-pack: center;
				-webkit-justify-content: center;
				justify-content: center;
		}

		.wal-label {
				white-space: nowrap;
				padding-left: 6px;
				padding-right: 12px;
				font-weight: 600;
				color: #5e5e5e;
				font-family: "Segoe UI", Frutiger, "Frutiger Linotype", "Dejavu Sans", "Helvetica Neue", Arial, sans-serif;
				font-size: 15px;
				-webkit-flex-shrink: 1;
				-moz-flex-shrink: 1;
				flex-shrink: 1;
				height: 21px;
				line-height: 21px;
		}
	</style>
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