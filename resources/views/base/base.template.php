<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Omega MVC — a modern PHP 8.4 framework with templates, routing, databases and more.">
    <title>{% yield('title') %}</title>

    {% if ($vite_has_manifest || $vite_hmr_script) %}
        {% vite(['resources/css/app.css', 'resources/js/app.js']) %}
    {% endif %}
</head>
<body class="bg-white antialiased selection:bg-indigo-600 selection:text-white">
    {% yield('content') %}
</body>
</html>