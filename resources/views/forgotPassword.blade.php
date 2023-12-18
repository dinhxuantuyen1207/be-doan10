<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel111</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
</head>

<body class="antialiased">
    <div>Xin Chào {{ $user }}! Đây là tin nhắn đến từ website : HK&B </div>
    <div style="text-align: center">Đây là mã thay đổi password mới của bạn<div>
            <div>
                <div
                    style="width: 400px;
            align-items: center;
            display: flex;
            background-color: #ccc;
            height: 75px;
            margin: auto;
            padding: auto;
            font-size: 50px;">
                    <p style="margin: auto;">{{ $code }}</p>
                </div>
            </div>
            <div style="text-align: center">Hãy đổi lại password mà bạn muốn</div>
</body>

</html>
