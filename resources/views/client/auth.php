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
              <div class="title">Ваш Личный кабинет</div>
              <div class="quote">Введите полученные логин и пароль.</div>
              <form action="/client" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="input-box"><label for="login">Логин</label><input name="login"/></div>
                <div class="input-box"><label for="password">Пароль</label><input name="password" type="password"/></div>
                <button type="submit">Go</button>
              </form>
            </div>
            <div class="content">
              <svg width="480" height="280">
                <defs>
                  <marker id = "chartMarker" viewBox = "0 0 8 8" refX = "8" refY = "4" markerWidth = "3" markerHeight = "3" stroke = "green" stroke-width = "2" fill = "none" orient = "auto">
                      <circle cx = "4" cy = "4" r = "2"/>
                  </marker>
                </defs>
                <line id="osX" x1="10" y1="270" x2="470" y2="270" stroke="#DB267D" stroke-width=".5" opacity="1"/>
                <line id="osY" x1="10" y1="270" x2="10" y2="10" stroke="#DB267D" stroke-width=".5" opacity="1"/>
                <path d = "M 10 250 L 70 246 L 130 239 L 190 237 L 260 231 L 300 214" stroke = "green" stroke-width = "2" fill = "none" marker-mid = "url(#chartMarker)"/>

              </svg>
            </div>
        </div>
    </body>
</html>
