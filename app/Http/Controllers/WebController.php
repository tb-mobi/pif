<?php
namespace mobi2\Http\Controllers;

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

class WebController extends Controller{
    public function welcome(Request $rq){
        if(TranzWare::isLogin())return redirect('/info');
        //if($rq->session()->has('phone'))return redirect('authenticate');
        return view('welcome');
    }
    public function index(Request $rq){
        TranzWare::SetSession($rq);
        if(!TranzWare::isLogin())return redirect('authenticate');
        $user=$rq->session()->get('user');
        try{
            $accounts=TranzWare::Accounts([]);
            $history=TranzWare::OperHistory([]);
            $products=TranzWare::Products([]);//($rq->session()->has('products'))?$rq->session()->get('products'):$this->products; //// TODO make adapter for Terrasoft to get
            $rates=TranzWare::GetRates([]);
            $user['products']=$products;
            $user['accounts']=$accounts;
            $user['history']=$history;
            $user['rates']=$rates;
        }
        catch(\Exception $e){
            $user['message']=$e->getCode();
        }
        return view('info',$user);
    }
    public function register(Request $rq){
        if($rq->has("phone")&&strlen($rq->input("phone"))){
            $arq=array(
                "fname"=>$rq->input("fname")
                ,"mname"=>$rq->input("mname")
                ,"sname"=>$rq->input("sname")
                ,"email"=>$rq->input("email")
                ,"phone"=>$rq->input("phone")
            );
            $rq->session()->put('phone',$rq->input("phone"));
            $rq->session()->put('email',$rq->input("email"));
            TranzWare::SetSession($rq);
            $ars=TranzWare::Register($arq);
            //if($rq->input("productCode")=="pif")
            return redirect('pinset');
        }
        else{
            return view('register');
        }
    }
    public function pinset(Request $rq){
        //$login=$rq->session()->has('phone')?$rq->session()->get('phone'):$rq->input('login');
        $login=$rq->has('login')?$rq->input('login'):$rq->session()->get('phone');
        $arq=[
            'login'=>$login
            ,'pin'=>substr($login,strlen($login)-4)
        ];
        TranzWare::SetSession($rq);
        if($rq->has('dynamicPassword')&&strlen($rq->input("dynamicPassword"))){
            $arq['newpin']=$rq->session()->get('newpin');
            $arq['DynamicPassword']=$rq->input("dynamicPassword");
            $ars=TranzWare::ChangePin($arq);
            return redirect('/authenticate');
        }
        else if($rq->has('newPin')&&strlen($rq->input("newPin"))){
            $arq['newpin']=$rq->input("newPin");
            $rq->session()->put('newpin',$arq['newpin']);
            $ars=TranzWare::ChangePin($arq);
            $arq['dynPass']=1;
        }
        return view('pinset',$arq);
    }
    public function authenticate(Request $rq){
        if($rq->has("login")&&strlen($rq->input("login"))){
            try{
                TranzWare::SetSession($rq);
                $ars=TranzWare::Login(['login'=>$rq->input('login'),'pin'=>$rq->input('password')]);
                $user=[
                    'authenticated'=>true,
                    'info'=>$ars,
                    'products'=>[] // TODO make adapter for Terrasoft to get
                ];
                $rq->session()->put('user',$user);
            }
            catch(TymException $e){
                // Make error message
            }
            return redirect('info');
        }
        $login=$rq->session()->has('phone')?$rq->session()->get('phone'):$rq->input('login');
        return view('authenticate',['login'=>$login]);
    }
    public function logout(Request $rq){
        if($rq->has("login")&&strlen($rq->input("login"))){
            try{
                TranzWare::Logout($rq);
                $ars=TranzWare::Login(['login'=>$rq->input('login'),'pin'=>$rq->input('password')]);
                $user=$rq->session()->pull('user');
            }
            catch(TymException $e){
                // Make error message
            }

        }
        return redirect('/authenticate');
    }
    /**
    * Protected section
    */
    protected $tranzWare;
    protected $store=array();
    protected $name='Semeon Petrovicher';
    protected $status='lead';
    protected $data=array(
      'id'=>'00001'
      ,'fname'=>'Simeon'
      ,'sname'=>'Sisdov'
      ,'mname'=>'Petrovich'
      //,'fio'=>'Sezam Aladin'
      ,'email'=>''
      ,'phone'=>''
    );
    protected $products=array(
      array('name'=>'pif'
        ,'balance'=>0
        ,'nominal'=>1000
        ,'currency'=>''
      )
    );
}
