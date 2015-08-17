<div class='content-block products'>
    <div class='title'>зПИФ</div>
<table width='480'>
    <tr><td class="right">Номинальная стоимость Пая:</td><td><i id="nominal">@include('product.models.amount',['currency'=>$rates['sell']['ToCurrency'],'amount'=>$rates['sell']['ToAmount']])</i></td><td></td></tr>
    <tr><td class="right">Текущая стоимость Пая:</td><td>@include('product.models.amount',['currency'=>$rates['buy']['FromCurrency'],'amount'=>$rates['buy']['FromAmount']]) <sup><i class="fa fa-sort-asc" style="color:#93c47d;"></i><i id="points"></i></sup></td></tr>
    <tr><td class="right">Кол-во паев: </td><td>@include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Available']])</td>
      <td>
        <a id='buypie' href="/pif/buy"><i class="fa fa-plus-circle"></i> Купить</a><br/>
        <a id='sellpie' href="/pif/sell"><i class="fa fa-minus-circle"></i> Продать</a>
      </td>
    </tr>
    <tr><td class="right">Эквивалент в рублях: </td><td><i class="fa fa-rub"></i><i id="converted"></i></td></tr>
    <tr><td class="right">Доход: </td><td><i class="fa fa-rub"></i><strong id="benefit"></strong></td></tr>
    <tr><td class="hr" colspan='3'></td></tr>
</table><br/>
<div class="pie-currency"> </div>
<div class="row"></div>
@include('product.pif.chart')
</div>
<!--
<m0:RateGroup>0</m0:RateGroup>
<m0:ExchangeDate>2015-08-04T00:00:00</m0:ExchangeDate>
<m0:FromCurrency>991</m0:FromCurrency>
<m0:ToCurrency>810</m0:ToCurrency>
<m0:FromAmount>1</m0:FromAmount>
<m0:ToAmount>1000</m0:ToAmount>
-->
