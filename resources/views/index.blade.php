<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>StudyProgress - University of Amsterdam</title>

    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>

    <link rel="stylesheet" href="{{ asset(mix('/css/toolkit.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('/css/app.css')) }}">

    <link rel="stylesheet" href="//cdn.jsdelivr.net/medium-editor/latest/css/medium-editor.min.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/medium-editor/latest/css/themes/bootstrap.min.css" type="text/css" media="screen" charset="utf-8">
</head>
<body>

<div id="app"></div>

<script type="text/javascript" src="{{ asset(mix('/js/manifest.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/vendor.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/vendor-load.js')) }}"></script>
<script type="text/javascript" src="{{ asset(mix('/js/app.js')) }}"></script>

</body>
</html>