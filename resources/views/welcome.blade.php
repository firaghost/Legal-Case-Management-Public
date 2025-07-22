<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
    <title>Redirecting...</title>
</head>
<body>
    <script>window.location.replace("{{ route('login') }}");</script>
    <noscript>
        <meta http-equiv="refresh" content="0; url={{ route('login') }}">
        <p>Redirecting to <a href="{{ route('login') }}">login</a>...</p>
    </noscript>
</body>
</html>






