@extends('layouts.master')
@section('title', 'зПИФ')
@section('sidebar')
    @parent
    <p>menu section</p>
@endsection
@section('content')
<div class="form-content">
  <div class="title">з<b>ПИФ</b><sup>2</sup></div>
    <div class="quote">Введите Ваше данные для приобритения ПИФ</div>
    <form action="/client/register" method="post">
      {!! csrf_field() !!}
      <input name="productCode" value="pif" type="hidden"/>
      <div class="input-box"><label for="fname">Имя</label><input name="fname" value="Влади"/></div>
      <div class="input-box"><label for="mname">Отчество</label><input name="mname" value="Серж"/></div>
      <div class="input-box"><label for="sname">Фамилия</label><input name="sname" value="Бушек"/></div>
      <div class="input-box"><label for="email">Email</label><input name="email" type="email" value="bushuev@mobiplas.ru"/></div>
      <div class="input-box"><label for="phone">Номер телефона</label><input name="phone" type="phone" value="+79265766710"/></div>
      <button type="submit">Go</button>
    </form>
</div>
@endsection
