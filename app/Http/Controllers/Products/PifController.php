<?php

namespace mobi2\Http\Controllers\Products;


use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Session;
use Illuminate\Support\Facades\Redirect;
use mobi2\Facades\TranzWare;
use mobi2\Http\Requests;
use mobi2\Http\Controllers\Controller;

use TymException;

class PifController extends ProductController{
    public function buy(Request $rq){
        TranzWare::SetSession($rq);
        if(!TranzWare::isLogin())return redirect('authenticate');
        $user=$rq->session()->get('user');
        if($rq->has('amount')&&strlen($rq->input("amount"))){
           $arq['Amount']=$rq->input("amount");
           $arq['Currency']=$rq->input("currency");
           $arq['FromAcct']=$rq->input("account");
           $arq['ToAcct']=$rq->input("pif");
           $arq['Comment']='Buy pie';
           $ars=TranzWare::Transfer($arq);
           return redirect('/info');
       }
        $user['products']=($rq->session()->has('products'))?$rq->session()->get('products'):[]; //// TODO make adapter for Terrasoft to get
        $user['accounts']=TranzWare::Accounts([]);
        $user['rates']=TranzWare::GetRates([]);
        return view('/product/pif/buy',$user);
    }
    public function sell(Request $rq){
        TranzWare::SetSession($rq);
        if(!TranzWare::isLogin())return redirect('authenticate');
        $user=$rq->session()->get('user');
        if($rq->has('amount')&&strlen($rq->input("amount"))){
           $arq['Amount']=$rq->input("amount");
           $arq['Currency']=$rq->input("currency");
           $arq['ToAcct']=$rq->input("account");
           $arq['FromAcct']=$rq->input("pif");
           $arq['Comment']='Buy pie';
           $ars=TranzWare::Transfer($arq);
           return redirect('/info');
       }
        $user['products']=($rq->session()->has('products'))?$rq->session()->get('products'):[]; //// TODO make adapter for Terrasoft to get
        $user['accounts']=TranzWare::Accounts([]);
        $user['rates']=TranzWare::GetRates([]);
        return view('/product/pif/sell',$user);
    }
}
