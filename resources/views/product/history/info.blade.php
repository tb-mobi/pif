<div class="content-block history">
    <div class='title'>История операций:</div>
    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Операция</th>
                <th>Сумма</th>
                <th>Где</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($history as $oper)
                <tr>
                    <td>@formatDate($oper['TranTime'])</td>
                    <td>@include('product.models.operation',['opercode'=>$oper['OperCode']])</td>
                    <td>@include('product.models.amount',['currency'=>$oper['OrigCurrency'],'amount'=>$oper['OrigAmount']])</td>
                    <td>{{$oper['TermCity']}} {{$oper['TermLocation']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('product.history.chart');
</div>
<!--
<m0:Date>2015-08-03T00:00:00</m0:Date>
<m0:OperCode>43</m0:OperCode>
<m0:Amount>-100</m0:Amount>
<m0:PAN>13982243936</m0:PAN>
<m0:MBR>0</m0:MBR>
<m0:TermClass>4</m0:TermClass>
<m0:TermName>VTBI</m0:TermName>
<m0:TermLocation>Moscow*Mobiplas*Moscow*Russia</m0:TermLocation>
<m0:TermSIC>6012</m0:TermSIC>
<m0:Type>1</m0:Type>
<m0:TranTime>2015-08-03T11:23:16</m0:TranTime>
<m0:OrigAmount>100</m0:OrigAmount>
<m0:OrigCurrency>810</m0:OrigCurrency>
<m0:AnotherTitle>Перевод со счета на счет через Telebank-терминал</m0:AnotherTitle>
<m0:ApprovalCode>779674</m0:ApprovalCode>
<m0:OrigCurrAlpha3Code>RUR</m0:OrigCurrAlpha3Code>
<m0:FrontId>3081</m0:FrontId>
<m0:OnlineIssuerFee>0</m0:OnlineIssuerFee>
<m0:IsMultiAcct>0</m0:IsMultiAcct>
<m0:SeqNo>1</m0:SeqNo>
<m0:TermCountry>643</m0:TermCountry>
<m0:TermCity>Moscow</m0:TermCity>
-->
