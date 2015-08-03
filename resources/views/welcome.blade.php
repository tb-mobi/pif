@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.menubar')
@endsection
@section('content')
    <div class="content">
        <div class="title"><b>Mobi</b>plas<sup>2</sup></div>
        <div class="quote">New age of Banking - Day2Day Bank.</div>
        <div class="block"><button class="a big" href="/register">Start</button>
    </div>
    <script language="JavaScript">
        $(document).ready(function() {
            $('button.a').click(function(e){
                e.preventDefault();
                var href=$(this).attr('href');
                window.location.href = href;
            });
        });
    </script>
@endsection
