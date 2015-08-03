@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.menubar')
@endsection
@section('content')
    <div class="content">
        <div class="title">Установка нового ПИН</div>
        @if(isset($dynPass))
            <div class="quote">Введите новый код подтверждения из SMS.</div>
            <form action="/pinset" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="login" value='{{$login}}'/>
                <div class="input-box"><label for="dynamicPassword">Код подтверждения</label><input name="dynamicPassword"/></div>
                <button type="submit">Go</button>
            </form>
        @else
            <div class="quote">Введите новый ПИН код.</div>
            <form action="/pinset" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                @if(isset($login)&&strlen($login))
                    <input type="hidden" name="login" value='{{$login}}'/>
                @else
                    <div class="input-box"><label for="login">Ваш мобильный</label><input class="login" name="login"/></div>
                @endif
                <div class="input-box"><label for="newPin">ПИН</label><input name="newPin" type="password"/></div>
                <button type="submit">Go</button>
            </form>
        @endif
    </div>
@endsection
