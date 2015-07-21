<html>
    <head>
        <title>Mobiplas - @yield('title')</title>
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href="/css/mobi.css" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Mono:100&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <script language="JavaScript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    </head>
    <body>
        <div class='sidebar'>
          @yield('sidebar')
      </div>
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
