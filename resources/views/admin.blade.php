<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <base href="{{ url('/admin') }}"/>
    <script>
        const user =@json(auth()->user()->only(['id','name']));
        const APP_NAME = "{{ env("APP_NAME") }}";
    </script>
    <link rel="stylesheet" href="{{mix("css/admin.css")}}">
</head>

<body style="padding: 0;margin: 0;box-sizing: border-box;">
<div id="app"></div>
<form method="POST" id="logout-form" action="{{ route('logout') }}">@csrf</form>
<script src="{{ mix('js/admin.js') }}"></script>
</body>

</html>
