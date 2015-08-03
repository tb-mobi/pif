@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.private-menubar')
@endsection
@section('content')
    <div class="content">
        <h1>Hi {{isset($info['fio'])?$info['fio']:'set Your Name'}}</h1>
        @if (isset($accounts))
            @include('product.accounts',['accounts'=>$accounts])
        @endif
        @if (isset($products))
            @foreach ($products as $product)
                @include('product.pif.info',['producs'=>$products])
            @endforeach
        @endif
        @if (isset($history))
            @foreach ($products as $product)
                @include('product.history.info',['history'=>$history])
            @endforeach
        @endif
    </div>
@endsection
