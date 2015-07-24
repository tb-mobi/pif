<!DOCTYPE html>
<html>
    <head>
        <title>PLATfon.client</title>
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Mono:100&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href="/css/mobi.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container">
            <div class="form-content">
              <div class="title">Установка нового ПИН</div>
              @if(isset($dynPass))
              <div class="quote">Введите новый код подтверждения из SMS.</div>
              <form action="/pinset" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="login" value='{{$login}}'/>
                <div class="input-box"><label for="dynamicPassword">Код подтверждения</label><input name="dynamicPassword"/></div>
                <button type="submit">Go</button>
              </form>
              @else
              <div class="quote">Введите новый ПИН код.</div>
              <form action="/pinset" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                @if(isset($login)&&strlen($login))
                <input type="hidden" name="login" value='{{$login}}'/>
                @else
                <div class="input-box"><label for="login">Ваш мобильный</label><input class="login" name="login"/></div>
                @endif
                <div class="input-box"><label for="newPin">ПИН</label><input name="newPin" type="password"/></div>
                <button type="submit">Go</button>
              </form>
              @endif
            </div>
        </div>
    </body>
</html>
