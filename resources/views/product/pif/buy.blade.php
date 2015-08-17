@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.private-menubar')
@endsection
@section('content')
    <div class="content">
        <div class="title">Покупка Паев</div>
        @if(isset($dynPass))
            <div class="quote">Введите новый код подтверждения из SMS.</div>
            <form action="/pif/buy" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="login" value='{{$login}}'/>
                <div class="input-box"><label for="dynamicPassword">Код подтверждения</label><input name="dynamicPassword"/></div>
                <button type="submit">Go</button>
            </form>
        @else
            <form action="/pif/buy" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="pif" value="{{$accounts['pif']['Acct']}}">
                <div class="input-box"><label for="account">Со счета:</label>@include('product.accounts-combo',['name'=>'account','accounts'=>$accounts])</div>
                <div class="input-box"><label for="amount">Введите кол-во паев:</label><input id="amount" name="amount" placeholder='например 5'/></div>
                <p>Сумма покупки: <i id="buyAmount">0 руб.</i></p>
                <button type="submit">Купить</button>
            </form>
        @endif
    </div>
@endsection
