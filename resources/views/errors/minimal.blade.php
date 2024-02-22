<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/js/app.js'])
    <title>TOP Error</title>


</head>

<body class="antialiased">
    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-800 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
            @yield('content')
        </div>
    </div>
</body>

</html>
