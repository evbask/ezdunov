<head>
    <meta charset="utf-8" />
    <title>{{ $page_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="{{$page_description}}">
    <meta name="keywords" content="{{$page_keywords}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="https://via.placeholder.com/30x30">

    <!-- Template CSS Files -->
    <!-- <link rel="stylesheet" type="text/css" href="/css/minified/index.css" /> -->
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/css/grandin/simple-line-icons.css">
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/css/skins/red.css" />
    <link rel="stylesheet" type="text/css" href="/css/picker/bootstrap-datetimepicker.min.css">
    @if (isset($grandin))
            {!! '<link href="/css/grandin/style.css" rel="stylesheet" type="text/css">' !!}
    @else
            {!! '<link href="/css/navs.css" rel="stylesheet" type="text/css">' !!}
    @endif
    <link rel="stylesheet" type="text/css" href="/css/grandin/jquery.toast.min.css">

    <!-- Revolution Slider CSS Files -->
    <link rel="stylesheet" type="text/css" href="/js/plugins/revolution/css/settings.css" />
    @if( !$nolayers || !isset($nolayers) )
    <link rel="stylesheet" type="text/css" href="/js/plugins/revolution/css/layers.css" />
    @endif
    @if( !$nonavigation  || !isset($nonavigation))
    <link rel="stylesheet" type="text/css" href="/js/plugins/revolution/css/navigation.css" />
    @endif

    <script src="/js/jquery-2.2.4.min.js"></script>

</head>