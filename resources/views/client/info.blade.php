@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @parent
    <p>menu section</p>
@endsection
@section('content')
    <div class="content">
        <h1>Hi {{isset($info['fio'])?$info['fio']:'set Your Name'}}</h1>
        @if (isset($products))
            @foreach ($products as $product)
                @include('product/info',['producs'=>$products])
            @endforeach
        @endif
    </div>
    <script language="JavaScript">
        $(document).ready(function() {
            $('#buypie,#sellpie,#balance-circle').click(function(e){
                e.preventDefault();
                var cur=parseFloat($('#currency').text().replace(' ',''));
                var href=$(this).attr('href');
                window.location.href = href+"?currency="+cur;
            });
        });
    </script>
@endsection
