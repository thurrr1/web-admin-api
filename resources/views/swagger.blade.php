<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <style>
        body { margin: 0; padding: 0; }
        .nav-bar {
            background-color: #1b1b1b;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            font-family: sans-serif;
            border-bottom: 3px solid #62a03f;
        }
        .nav-bar a.btn {
            background-color: #62a03f;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .nav-bar a.btn:hover {
            background-color: #4e862d;
        }
        #swagger-ui { width: 100%; height: calc(100vh - 54px); overflow-y: scroll; }
    </style>
</head>
<body>
<div class="nav-bar">
    <div style="font-weight: bold; font-size: 18px;">{{ $title ?? 'API Documentation' }}</div>
    <div>
        @if(isset($otherDocsUrl))
            <a href="{{ $otherDocsUrl }}" class="btn">{{ $otherDocsLabel ?? 'Switch API' }}</a>
        @endif
    </div>
</div>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js" crossorigin></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            url: "{{ $url }}",
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            layout: "BaseLayout",
        });
    };
</script>
</body>
</html>
