<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administration | ProConnect</title>
    @vite('resources/js/admin/main.js')
</head>

<body class="antialiased bg-slate-950">
    <div id="admin-app"></div>
</body>

</html>
