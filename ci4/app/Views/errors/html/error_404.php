<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>404 - Page Not Found</title>

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
			color: #fd7e14;
		}

		.wrap {
			max-width: 1024px;
			margin: 5rem auto;
			padding: 2rem;
			background: #fff;
			text-align: center;
			border: 3px solid #fd7e14;
			border-radius: 0.5rem;
			position: relative;
			box-shadow: 0 4px 6px rgba(253, 126, 20, 0.1);
		}

		.error-icon {
			font-size: 4rem;
			color: #fd7e14;
			margin-bottom: 1rem;
		}

		.error-message {
			background: #fff3cd;
			border: 2px solid #ffc107;
			border-radius: 8px;
			padding: 1.5rem;
			margin: 1.5rem 0;
			color: #856404;
			font-size: 1.1rem;
			font-weight: 500;
			line-height: 1.6;
		}

		.error-title {
			font-weight: 700;
			font-size: 1.3rem;
			margin-bottom: 0.5rem;
			color: #fd7e14;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		.error-details {
			background: #f8f9fa;
			border: 1px solid #dee2e6;
			border-radius: 6px;
			padding: 1rem;
			margin: 1rem 0;
			font-family: 'Courier New', monospace;
			font-size: 0.95rem;
			color: #495057;
			word-break: break-all;
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

		.help-info {
			margin-top: 2rem;
			padding: 1rem;
			background: #e7f3ff;
			border: 1px solid #0dcaf0;
			border-radius: 6px;
			color: #055160;
		}

		.help-info strong {
			color: #0d6efd;
		}

		.suggestions {
			margin-top: 1.5rem;
			text-align: left;
			display: inline-block;
		}

		.suggestions li {
			margin: 0.5rem 0;
			color: #495057;
		}

		.back-button {
			display: inline-block;
			margin-top: 1.5rem;
			padding: 0.75rem 1.5rem;
			background: #fd7e14;
			color: white !important;
			text-decoration: none;
			border-radius: 6px;
			font-weight: 600;
			transition: background 0.3s;
		}

		.back-button:hover {
			background: #e66a0a;
			color: white !important;
			text-decoration: none;
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
			color: #fd7e14;
			font-weight: 600;
		}
	</style>
</head>

<body>
	<div class="wrap">
		<div class="error-icon">🔍</div>
		<h1>404 - PAGE NOT FOUND</h1>

		<div class="error-message">
			<div class="error-title">The page you're looking for doesn't exist</div>
			<?php if (!empty($message) && $message !== '(null)') : ?>
				<?= nl2br(esc($message)) ?>
			<?php else : ?>
				The section or page you are trying to access cannot be found. The URL may be incorrect or the page may have been moved or deleted.
			<?php endif ?>
		</div>

		<?php if (isset($_SERVER['REQUEST_URI'])) : ?>
		<div class="error-details">
			<strong>Requested URL:</strong> <?= esc($_SERVER['REQUEST_URI']) ?>
		</div>
		<?php endif; ?>

		<div class="help-info">
			<strong>What can you do?</strong>
			<div class="suggestions">
				<ul>
					<li>Check the URL for typos or spelling errors</li>
					<li>Use your browser's back button to return to the previous page</li>
					<li>Visit the homepage and navigate from there</li>
					<li>Contact your system administrator if you believe this is an error</li>
				</ul>
			</div>
		</div>

		<a href="/" class="back-button">← Go to Homepage</a>
	</div>
</body>

</html>
