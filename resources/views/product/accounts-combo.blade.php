<select name="{{$name}}">
    @foreach ($accounts as $account)
    <option value="{{$account['Acct']}}">
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
         @include('product.models.amount',['currency'=>$account['Currency'],'amount'=>$account['Available']])
    </option>
    @endforeach
</select>
