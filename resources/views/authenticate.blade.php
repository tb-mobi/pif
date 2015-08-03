@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.menubar')
@endsection
@section('content')
    <div class="content">
        <div class="title">Ваш Личный кабинет</div>
        <div class="quote">Введите полученные логин и пароль.</div>
        <form action="/authenticate" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="input-box"><label for="login">Логин</label><input name="login" value='{{$login}}'/></div>
            <div class="input-box"><label for="password">Пароль</label><input name="password" type="password"/></div>
            <button type="submit">Go</button>
        </form>
    </div>
    <div class="content">
        <svg width="480" height="280">
            <defs>
                <marker id = "chartMarker" viewBox = "0 0 8 8" refX = "8" refY = "4" markerWidth = "3" markerHeight = "3" stroke = "green" stroke-width = "2" fill = "none" orient = "auto">
                    <circle cx = "4" cy = "4" r = "2"/>
                </marker>
            </defs>
            <line id="osX" x1="10" y1="270" x2="470" y2="270" stroke="#DB267D" stroke-width=".5" opacity="1"/>
            <line id="osY" x1="10" y1="270" x2="10" y2="10" stroke="#DB267D" stroke-width=".5" opacity="1"/>
            <path d = "M 10 250 L 70 246 L 130 239 L 190 237 L 260 231 L 300 214" stroke = "green" stroke-width = "2" fill = "none" marker-mid = "url(#chartMarker)"/>
        </svg>
    </div>
@endsection
