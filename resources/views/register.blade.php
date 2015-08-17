@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @include('layouts.menubar')
@endsection
@section('content')
<div class="form-content">
  <div class="title">з<b>ПИФ</b><sup>2</sup></div>
    <div class="quote">Введите Ваше данные для приобритения ПИФ</div>
    <form action="register" method="post">
      {!! csrf_field() !!}
      <input name="productCode" value="pif" type="hidden"/>
      <div class="input-box"><label for="fname">Имя</label><input name="fname" placeholder="Имя"/></div>
      <div class="input-box"><label for="mname">Отчество</label><input name="mname" placeholder="Отчество"/></div>
      <div class="input-box"><label for="sname">Фамилия</label><input name="sname" placeholder="Фамилия"/></div>
      <div class="input-box"><label for="email">Email</label><input name="email" type="email" placeholder="email"/></div>
      <div class="input-box"><label for="phone">Номер телефона</label><input name="phone" type="phone" placeholder="Номер телефона"/></div>
      <button type="submit">Go</button>
    </form>
</div>
@endsection
