<!DOCTYPE html>
<html>
    <head>
        <title>PLATfon.client</title>
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Mono:100&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href="/css/mobi.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="form-content">
              <div class="title">Продажа</div>
              <p>Текущая стоимость Пая: <i id="currency"><?php echo $products[0]['currency'];?> руб.</i></p>
              <p>У Вас: <i id="pif-balance"><?php echo $products[0]['balance'];?></i></p>
              <form action="/client/soldpie" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="input-box"><label for="amount">Введите кол-во паев: </label><input id="amount" name="amount" value="0"/></div>
                <p>Сумма сделки: <i id="buyAmount">0 руб.</i></p>
                <button type="submit">Продать</button>
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
        <script language="JavaScript">
          $('#amount').focus().val('');
          $('#amount').keyup(function() {
              var amt=parseInt($(this).val());
              if(amt>0){
              var cur=parseFloat($('#currency').text().replace(' ',''));
              var bamt=cur*amt;
              $('#buyAmount').text(bamt.toFixed(2)+" руб.");
            }
            }).click(function() {
              $( this ).val('');
              $( this ).keyup();
          });
        </script>
    </body>
</html>
