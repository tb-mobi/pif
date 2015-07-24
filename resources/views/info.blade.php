@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @parent
    <p>menu section</p>
@endsection
@section('content')
    <div class="content">
        <h1>Hi {{isset($info['fio'])?$info['fio']:'set Your Name'}}</h1>
        @if (isset($accounts))
            <table class="account">
            @foreach ($accounts as $account)
                @include('product/account',['account'=>$account])
            @endforeach
        </table>
        <br/>
        @endif
        @if (isset($products))
            @foreach ($products as $product)
                @include('product/info',['producs'=>$products])
            @endforeach
        @endif
    </div>
@endsection
