<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')

    <title>{{ $title }}</title>

    <style>
        html, body {
            background-color: #121212;
            color: rgb(255, 255, 255);
            font-family: "Plus_Jakarta_Sans", "Roboto", "Segoe UI", sans-serif;
            box-sizing: border-box;
        }

        .card {
            border-radius: 6px;
            background-color: rgb(40, 40, 40);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 10px 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px auto;
            padding: 10px 20px;
        }
        .card:hover {
            background-color: #575757;
        }
    </style>
    
    @stack("style")

</head>
<body>
    <h1 class="text-center font-semibold text-3xl py-10">{{ $title }}</h1>

    @yield('content')
</body>
</html>