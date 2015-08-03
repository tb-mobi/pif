<html>
<head>
    <title>Mobiplas - @yield('title')</title>
    <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link href='//fonts.googleapis.com/css?family=Roboto+Mono:100&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="/css/mobi.css" rel="stylesheet" type="text/css">
    <link href="/css/menubar.css" rel="stylesheet" type="text/css">
    <link href="/css/forms.css" rel="stylesheet" type="text/css">
    <link href="/css/content.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>
<body>
    <div class='sidebar'>
        @yield('sidebar')
    </div>
    <div class="container">
        @if (Session::has('message'))
	       <div class="flash alert-info">
		             <p>{{ Session::get('message') }}</p>
	        </div>
        @endif
        @yield('content')
    </div>
</body>
</html>
