<table width='480'>
    <tr><td class="right">Номинальная стоимость Пая:</td><td><i class="fa fa-rub"></i><i id="nominal">1 000</i></td><td></td></tr>
    <tr><td class="right">Текущая стоимость Пая:</td><td><i class="fa fa-rub"></i><i id="currency">1 043.34</i> <sup><i class="fa fa-sort-asc" style="color:#93c47d;"></i><i id="points"></i></sup></td></tr>
    <tr><td class="right">Кол-во паев: </td><td><i class="fa fa-rub"></i><i id="pif-balance">{{isset($product['balance']) ? $product['balance'] : '0.00'}}</i></td>
      <td>
        <a id='buypie' href="/client/buypie"><i class="fa fa-plus-circle"></i> Купить</a><br/>
        <a id='sellpie' href="/client/sellpie"><i class="fa fa-minus-circle"></i> Продать</a>
      </td>
    </tr>
    <tr><td class="right">Эквивалент в рублях: </td><td><i class="fa fa-rub"></i><i id="converted"></i></td></tr>
    <tr><td class="right">Доход: </td><td><i class="fa fa-rub"></i><strong id="benefit"></strong></td></tr>
    <tr><td class="hr" colspan='3'></td></tr>
</table><br/>
<div class="pie-currency"> </div>
<div class="row"></div>
<svg width="100%" height="160">
  <line id="balance-line-left" x1="50%" y1="80" x2="50%" y2="80" stroke="#DB267D" stroke-width="1" opacity="1"/>
  <line id="balance-line-right" x1="50%" y1="80" x2="50%" y2="80" stroke="#DB267D" stroke-width="1" opacity="1"/>
  <circle id="balance-circle" class="balance-circle" cx="50%" cy="80" r="60" stroke="none" fill="#DB267D" style="cursor:pointer" opacity="1."></circle>
  <text id="pif-balance-text" x="50%" y="84" fill="white" text-anchor="middle" style="font-weight:bold;">{{isset($product['balance']) ? $product['balance'] : '0.00'}} {{isset($product['currency']) ? $product['currency'] : 'руб.'}}</text>
  <animate xlink:href="#balance-circle" attributeName="r" from="60" to="80" dur="0.2s" begin="mouseover" fill="freeze" id="circle-anim-over"/>
  <animate xlink:href="#balance-line-left" attributeName="x1" from="50%" to="0" dur="0.2s" begin="circle-anim-over.begin+.1s" fill="freeze" id="circle-anim-over-lineL"/>
  <animate xlink:href="#balance-line-right" attributeName="x2" from="50%" to="100%" dur="0.2s" begin="circle-anim-over.begin+.1s" fill="freeze" id="circle-anim-over-lineR"/>
  <animate xlink:href="#balance-circle" attributeName="r" from="80" to="60" dur="0.2s" begin="mouseout" fill="freeze" id="circle-anim-out"/>
  <animate xlink:href="#balance-line-left" attributeName="x1" from="0" to="50%" dur="0.2s" begin="circle-anim-out.begin+.1s" fill="freeze" id="circle-anim-out-lineL"/>
  <animate xlink:href="#balance-line-right" attributeName="x2" from="100%" to="50%" dur="0.2s" begin="circle-anim-out.begin+.1s" fill="freeze" id="circle-anim-out-lineR"/>
</svg>
<svg id="graph" width="480" height="280">
    <defs>
      <marker id = "chartMarker" viewBox = "0 0 8 8" refX = "8" refY = "4" markerWidth = "4" markerHeight = "4" stroke = "green" stroke-width = "1" fill = "none" orient = "auto">
          <circle cx = "4" cy = "4" r = "3"/>
      </marker>
    </defs>
    <line id="osX" x1="10" y1="270" x2="470" y2="270" stroke="#DB267D" stroke-width=".5" opacity="1"/>
    <line id="osY" x1="10" y1="270" x2="10" y2="10" stroke="#DB267D" stroke-width=".5" opacity="1"/>
    <!--<path d = "M 10 250 L 70 246 L 130 239 L 190 237 L 260 231 L 300 214" stroke = "green" stroke-width = "2" fill = "none" marker-mid = "url(#chartMarker)"/>-->
    <path id="chart" d = "M 10 250" stroke = "green" stroke-width = "2" fill = "none" marker-mid = "url(#chartMarker)"/>
  </svg>
  <script language="JavaScript">
    $(document).ready(function() {
    var currency=1043.34;
    var step=10;
    var xStep=(parseInt($('#graph').attr('width'))-20)/step;
    var cnt=0;
    setInterval(function(){
      if(cnt>=step)return;
      var nom=parseFloat($('#nominal').text().replace(' ',''));
      cnt++;
      var curOld=parseFloat($('#currency').text().replace(' ',''));
      var dlt=.017864*Math.random();
      cur=curOld+curOld*dlt;
      var yy=1280-parseInt(cur);
      var xx=xStep*cnt+xStep;
      console.log(" L "+xx+" "+yy);

      var amt=parseInt($('#pif-balance-text').text());
      var num=cur*amt;
      str=num.toFixed(2);
      console.log(nom+" / "+cur+" x100%");
      var poi=100*(cur/nom-1);

      var ben=num-nom*amt;

      //draw
      $('#chart').attr("d",$('#chart').attr("d")+" L "+xx+" "+yy);
      //text
      $('#converted').text(str);
      $('#currency').text(cur.toFixed(2));
      $('#points').text(poi.toFixed(2)+'%');
      $('#benefit').text(ben.toFixed(2));

    },1760);
});
</script>
