
<tr>
    <td class="right">Номер счета {{$account['Acct']}} - @include('product.models.status',['status'=>$account['Status']])</td>
    <!--<td class="right">{{$account['Type']}}</td>-->
    <td class="right">
        Баланс - @include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Leger']])
    </td>
    <td class="right">
        Доступно - @include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Available']])
    </td>
</tr>
