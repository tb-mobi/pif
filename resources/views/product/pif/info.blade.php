<div class='content-block products'>
    <div class='title'>зПИФ</div>
<table width='480'>
    <tr><td class="right">Номинальная стоимость Пая:</td><td><i class="fa fa-rub"></i><i id="nominal">1 000</i></td><td></td></tr>
    <tr><td class="right">Текущая стоимость Пая:</td><td><i class="fa fa-rub"></i><i id="currency">1 043.34</i> <sup><i class="fa fa-sort-asc" style="color:#93c47d;"></i><i id="points"></i></sup></td></tr>
    <tr><td class="right">Кол-во паев: </td><td><i class="fa fa-rub"></i><i id="pif-balance">{{isset($product['balance']) ? $product['balance'] : '0.00'}}</i></td>
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
