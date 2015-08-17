<tr>
    <td class="right">
        @if ($account['product']=='pif')
            {{'зПИФ'}}
        @elseif ($account['product']=='deb')
            {{'Доходный счет'}}
        @elseif ($account['product']=='dep')
            {{'Депозит'}}
        @elseif ($account['product']=='pre')
            {{'Предоплаченная карта'}}
        @else
            {{'Текущий счет'}}
        @endif
    </td>
    <td class="right">{{$account['Acct']}} - @include('product.models.status',['status'=>$account['Status']])</td>
    <!--<td class="right">{{$account['Type']}}</td>-->
    <td class="right">
        Баланс - @include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Leger']])
    </td>
    <td class="right">
        Доступно - @include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Available']])
    </td>
</tr>
