<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API docs — Energx</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css">
  <style>
    body { margin:0; }
    .top { padding: 10px 14px; background:#0F4A2A; color:#fff; font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    .top a { color:#fff; text-decoration:none; font-weight:700; }
  </style>
</head>
<body>
  <div class="top"><a href="/">Energx</a> · API docs</div>
  <div id="swagger-ui"></div>

  <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script>
    window.ui = SwaggerUIBundle({
      url: "/openapi.json",
      dom_id: "#swagger-ui",
      presets: [SwaggerUIBundle.presets.apis],
      layout: "BaseLayout"
    });
  </script>
</body>
</html>

