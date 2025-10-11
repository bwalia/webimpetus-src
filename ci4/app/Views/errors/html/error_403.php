<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>403 Access denied</title>

	<style>
		div.logo {
			height: 200px;
			width: 155px;
			display: inline-block;
			opacity: 0.08;
			position: absolute;
			top: 2rem;
			left: 50%;
			margin-left: -73px;
		}

		body {
			height: 100%;
			background: #fafafa;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			color: #777;
			font-weight: 300;
		}

		h1 {
			font-weight: 600;
			letter-spacing: 0.8;
			font-size: 3rem;
			margin-top: 0;
			margin-bottom: 1rem;
			color: #dc3545;
		}

		.wrap {
			max-width: 1024px;
			margin: 5rem auto;
			padding: 2rem;
			background: #fff;
			text-align: center;
			border: 3px solid #dc3545;
			border-radius: 0.5rem;
			position: relative;
			box-shadow: 0 4px 6px rgba(220, 53, 69, 0.1);
		}

		.error-icon {
			font-size: 4rem;
			color: #dc3545;
			margin-bottom: 1rem;
		}

		.error-message {
			background: #f8d7da;
			border: 2px solid #dc3545;
			border-radius: 8px;
			padding: 1.5rem;
			margin: 1.5rem 0;
			color: #721c24;
			font-size: 1.1rem;
			font-weight: 500;
			line-height: 1.6;
		}

		.error-title {
			font-weight: 700;
			font-size: 1.3rem;
			margin-bottom: 0.5rem;
			color: #dc3545;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		pre {
			white-space: normal;
			margin-top: 1.5rem;
		}

		code {
			background: #fafafa;
			border: 1px solid #efefef;
			padding: 0.5rem 1rem;
			border-radius: 5px;
			display: block;
		}

		p {
			margin-top: 1.5rem;
		}

		.contact-info {
			margin-top: 2rem;
			padding: 1rem;
			background: #fff3cd;
			border: 1px solid #ffc107;
			border-radius: 6px;
			color: #856404;
		}

		.footer {
			margin-top: 2rem;
			border-top: 1px solid #efefef;
			padding: 1em 2em 0 2em;
			font-size: 85%;
			color: #999;
		}

		a:active,
		a:link,
		a:visited {
			color: #dc3545;
			font-weight: 600;
		}
	</style>
</head>

<body>
	<div class="wrap">
		<div class="error-icon">🚫</div>
		<h1>403 - ACCESS DENIED</h1>

		<div class="error-message">
			<div class="error-title">Permission Required</div>
			<?php if (!empty($message) && $message !== '(null)') : ?>
				<?= nl2br(esc($message)) ?>
			<?php else : ?>
				You do not have permission to access this section. This area is restricted and requires specific authorization.
			<?php endif ?>
		</div>

		<div class="contact-info">
			<strong>Need Access?</strong><br>
			Please contact your system administrator to request the necessary permissions for this module.
		</div>
	</div>
</body>

</html>