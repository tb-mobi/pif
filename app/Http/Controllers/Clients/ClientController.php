<?php

namespace mobi2\Http\Controllers\Clients;

use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Session;
use Illuminate\Support\Facades\Redirect;
use mobi2\Http\Requests;
use mobi2\Http\Controllers\Controller;
use mobi2\Adapters\TranzWare;

use TymException;
class ClientController extends Controller
{
    public function __construct(){
    }
    public function getIndex(Request $rq){
        return $this->index($rq);
    }
    public function index(Request $rq){
        if(!$rq->session()->has('user'))return redirect('client/authenticate');
        $user=$rq->session()->get('user');
        $user['products']=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products; //// TODO make adapter for Terrasoft to get
        if(!$user['authenticated'])return redirect('client/authenticate');
        return view('client/info',$user);
    }
    public function postIndex(Request $rq){
        return $this->index($rq);
    }
    public function getBuypie(Request $rq){
      $this->data=($rq->session()->has('client'))?$rq->session()->get('client'):$this->data;
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      $this->products[0]['currency']=$rq->input('currency');
      return view('product/buypie',[
          'client'=>$this->data
          ,'products'=>$this->products
      ]);
    }
    public function getSellpie(Request $rq){
      $this->data=($rq->session()->has('client'))?$rq->session()->get('client'):$this->data;
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      $this->products[0]['currency']=$rq->input('currency');
      return view('product/sellpie',[
        'client'=>$this->data
        ,'products'=>$this->products
      ]);
    }
    public function postBuyedpie(Request $rq){
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      $this->products[0]['balance']=$this->products[0]['balance']+$rq->input('amount');
      $rq->session()->put('products',$this->products);
      return redirect('client');
    }
    public function postSoldpie(Request $rq){
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      $this->products[0]['balance']=$this->products[0]['balance']-$rq->input('amount');
      $rq->session()->put('products',$this->products);
      return redirect('client');
    }
    public function postRegister(Request $rq){
      $client=$this->create($rq);
      if($rq->input("productCode")=="pif")return redirect('product/register');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $rq){
      $client=array(
        "fname"=>$rq->input("fname")
        ,"mname"=>$rq->input("mname")
        ,"sname"=>$rq->input("sname")
        ,"email"=>$rq->input("email")
        ,"phone"=>$rq->input("phone")
      );
      $rq->session()->put('phone',$rq->input("phone"));
      $rq->session()->put('email',$rq->input("email"));
      $client=$this->tw->Register($client);

      return $client;
    }
    public function pinset(Request $rq,VTBIAdapter $tw){
        $login=$rq->session()->has('phone')?$rq->session()->get('phone'):$rq->input('login');
        $arq=[
            'login'=>$login
            ,'pin'=>substr($login,strlen($login)-4)
        ];
        if($rq->has('dynamicPassword')&&strlen($rq->input("dynamicPassword"))){
            $arq['newpin']=$rq->session()->get('newpin');
            $arq['DynamicPassword']=$rq->input("dynamicPassword");
            //$ars=$this->tw->ChangePin($arq);
            return redirect('client/authenticate');
        }
        else if($rq->has('newPin')&&strlen($rq->input("newPin"))){
            $arq['newpin']=$rq->input("newPin");
            $rq->session()->put('newpin',$arq['newpin']);
            $ars=$tw->Logon($arq);
            //$ars=$this->tw->ChangePin($arq);
            $arq['dynPass']=1;
        }
        return view('client/pinset',$arq);
    }
    public function getPinset(Request $rq,TranzWare $tw){
        return $this->pinset($rq);
    }
    public function postPinset(Request $rq,TranzWare $tw){
        return $this->pinset($rq);
    }
    public function getAuthenticate(Request $rq){
      return view('client/auth',['login'=>($rq->session()->has('login')?$rq->session()->get('login'):'')]);
    }
    public function postAuthenticate(Request $rq){
        try{
            $ars=$this->tw->Login(['login'=>$rq->input('login'),'pin'=>$rq->input('password')]);
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
        return redirect('client');
    }
    /**
    * Protected section
    */
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
