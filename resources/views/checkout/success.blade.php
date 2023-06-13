<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Thanks for your order!</title>

</head>

<body class="antialiased">
    <h1>Thanks for your order!</h1>
    <p>
        Hi, {{ $customer->name }}
    </p>
    <p>
        We appreciate your business!
        If you have any questions, please email
        <a href="mailto:orders@example.com">orders@example.com</a>.
    </p>
</body>

</html>