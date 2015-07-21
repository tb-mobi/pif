<!DOCTYPE html>
<html>
    <head>
        <title>PLATfon</title>
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Mono:100&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href="/css/mobi.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="content">
              <h1>Hi <?php echo $name;?></h1>
              <svg width="100%" height="160">
                <line id="balance-line-left" x1="50%" y1="80" x2="50%" y2="80" stroke="#DB267D" stroke-width="1" opacity="1"/>
                <line id="balance-line-right" x1="50%" y1="80" x2="50%" y2="80" stroke="#DB267D" stroke-width="1" opacity="1"/>
                <circle id="balance-circle" class="balance-circle" cx="50%" cy="80" r="60" stroke="none" fill="#DB267D" style="cursor:pointer" opacity="1."></circle>
                <text id="pif-balance-text" x="50%" y="84" fill="white" text-anchor="middle" style="font-weight:bold;"><?php echo $product['balance'].' '.$product['currency'];?></text>
                <animate xlink:href="#balance-circle" attributeName="r" from="60" to="80" dur="0.2s" begin="mouseover" fill="freeze" id="circle-anim-over"/>
                <animate xlink:href="#balance-line-left" attributeName="x1" from="50%" to="0" dur="0.2s" begin="circle-anim-over.begin+.1s" fill="freeze" id="circle-anim-over-lineL"/>
                <animate xlink:href="#balance-line-right" attributeName="x2" from="50%" to="100%" dur="0.2s" begin="circle-anim-over.begin+.1s" fill="freeze" id="circle-anim-over-lineR"/>
                <animate xlink:href="#balance-circle" attributeName="r" from="80" to="60" dur="0.2s" begin="mouseout" fill="freeze" id="circle-anim-out"/>
                <animate xlink:href="#balance-line-left" attributeName="x1" from="0" to="50%" dur="0.2s" begin="circle-anim-out.begin+.1s" fill="freeze" id="circle-anim-out-lineL"/>
                <animate xlink:href="#balance-line-right" attributeName="x2" from="100%" to="50%" dur="0.2s" begin="circle-anim-out.begin+.1s" fill="freeze" id="circle-anim-out-lineR"/>
              </svg>
            </div>
        </div>
        <script language="JavaScript">
          setInterval(function(){
            var txt=$('#pif-balance-text').text().split(' ');
            var num=parseFloat(txt[0]);
            var dlt=.000030864;
            num=num+num*dlt;
            str=num.toFixed(2)+" "+txt[1];
            $('#pif-balance-text').text(str);

          },760);
          /*var circleRadiusDelta=10;
          $('.circle').mouseenter(function(){
            $(this).attr('r',parseInt($(this).attr('r'))+circleRadiusDelta);
          }).mouseout(function(){
            $(this).attr('r',parseInt($(this).attr('r'))-circleRadiusDelta);
          });*/
        </script>
    </body>
</html>
