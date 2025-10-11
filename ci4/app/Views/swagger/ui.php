<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebAImpetus API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.10.5/swagger-ui.css">
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .swagger-ui .topbar {
            background-color: #2c3e50;
        }

        .swagger-ui .topbar .download-url-wrapper {
            display: none;
        }

        .swagger-ui .info .title {
            color: #3498db;
        }

        .custom-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .custom-header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 700;
        }

        .custom-header p {
            margin: 10px 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }

        .auth-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px;
            border-radius: 4px;
        }

        .auth-info h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .auth-info code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            color: #d63384;
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <h1>🚀 MyWorkstation API</h1>
        <p>Complete API Documentation with Interactive Testing</p>
    </div>

    <div class="auth-info">
        <h3>🔐 Authentication</h3>
        <p>This API uses <strong>Bearer Token Authentication</strong>. To authorize:</p>
        <ol>
            <li>Click the <strong>"Authorize"</strong> button (🔓 icon) at the top right</li>
            <li>Enter your JWT token in the format: <code>Bearer YOUR_TOKEN_HERE</code></li>
            <li>Click <strong>"Authorize"</strong> and then <strong>"Close"</strong></li>
            <li>All subsequent API requests will include your authorization token</li>
        </ol>
        <p><strong>Note:</strong> Most endpoints require <code>uuid_business_id</code> parameter to filter data by business.</p>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.10.5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.10.5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            // Build a system
            const ui = SwaggerUIBundle({
                url: "/swagger/json",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                docExpansion: "list",
                filter: true,
                showRequestHeaders: true,
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                validatorUrl: null,
                persistAuthorization: true,
                tryItOutEnabled: true,
                displayRequestDuration: true,
                requestInterceptor: function(request) {
                    // Log requests for debugging
                    console.log('API Request:', request);
                    return request;
                },
                responseInterceptor: function(response) {
                    // Log responses for debugging
                    console.log('API Response:', response);
                    return response;
                }
            });

            window.ui = ui;

            // Auto-expand the first tag on load
            setTimeout(function() {
                const firstTag = document.querySelector('.opblock-tag-section');
                if (firstTag && !firstTag.classList.contains('is-open')) {
                    const button = firstTag.querySelector('button');
                    if (button) button.click();
                }
            }, 500);
        };
    </script>
</body>
</html>
