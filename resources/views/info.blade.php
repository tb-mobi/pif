@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.private-menubar')
    <ul class="user-func">
        <li><i class="fa fa-user"></i> {{isset($info['fio'])?$info['fio']:'set Your Name'}}</li>
    </ul>
@endsection
@section('content')
    <div class="content">
        @if (isset($message))
            <div class="quote">{{$message}}</div>
        @endif
        @if (isset($accounts))
            @include('product.accounts',['accounts'=>$accounts])
        @endif
        @if (isset($products))
            @foreach ($products as $product)
                @if(isset($accounts['pif']))
                    @include('product.pif.info',['producs'=>$products,'rates'=>$rates,'account'=>$accounts['pif']])
                @endif
            @endforeach
        @endif
        @if (isset($history))
            @foreach ($products as $product)
                @include('product.history.info',['history'=>$history])
            @endforeach
        @endif
    </div>
@endsection
