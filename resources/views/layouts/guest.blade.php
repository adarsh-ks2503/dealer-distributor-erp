<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <!-- Favicons -->
     <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
     <link href="{{ asset('assets/img/logo.png') }}" rel="apple-touch-icon">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>SINGHAL STEEL</title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="  flex-col items-center" style="align-items: baseline;">
                <a href="{{ url('/') }}" class="logo flex flex-col items-center py-4" style="width: 390px">
                    <img style="width: 20rem;height: 8rem;" class="  fill-current text-gray-500" src="{{ asset('assets/img/logo.png') }}"
                        alt="">
                    {{-- <span class="mt-2 text-lg font-semibold">SunilSteel</span> --}}
                </a>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>

</html>
