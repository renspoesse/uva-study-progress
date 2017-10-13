<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>

    <link rel="stylesheet" href="{{ asset(mix('/css/toolkit.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('/css/app.css')) }}">

    <link rel="stylesheet" href="//cdn.jsdelivr.net/medium-editor/latest/css/medium-editor.min.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/medium-editor/latest/css/themes/bootstrap.min.css" type="text/css" media="screen" charset="utf-8">
</head>
<body>

<div id="app"></div>

<script type="text/javascript" src="{{ asset('/js/lang.js') }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/manifest.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/vendor.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/vendor-load.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/app.js')) }}"></script>

</body>
</html>