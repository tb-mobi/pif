<?php

namespace mobi2\Http\Controllers\Clients;

use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Session;

use mobi2\Http\Requests;
use mobi2\Http\Controllers\Controller;
use mobi2\Http\Controllers\Controller\Clients\Client;

use MClient;
class ClientController extends Controller
{
    public function __construct(MClient $adp){
      $this->tw=$adp;
    }
      /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Request $rq){
      $this->data=($rq->session()->has('client'))?$rq->session()->get('client'):$this->data;
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      return view('client/info',[
          'client'=>$this->data
          ,'products'=>$this->products
      ]);
    }
    public function index(Request $rq){
        return $this->getIndex($rq);
    }
    public function postIndex(Request $rq){
        return $this->getIndex($rq);
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
    protected function randomString(){
      $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $randstring = '';
      for ($i = 0; $i < 10; $i++) {
          $randstring.= $characters[rand(0, strlen($characters))];
      }
      return $randstring;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request){
      $client=array(
        "fname"=>$request->input("fname")
        ,"mname"=>$request->input("mname")
        ,"sname"=>$request->input("sname")
        ,"email"=>$request->input("email")
        ,"phone"=>$request->input("phone")
      );
      $client=$this->tw->Register($client);
      return $client;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($client,Request $request){
      return "Ok";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id){
        //
    }
    public function getAuthenticate(MClient $tw){
      return view('client/auth',['login'=>$tw->login]);
    }
    public function postAuthenticate(Request $rq,MClient $tw){
      $ars=$tw->Login(['login'=>$rq->input('login'),'pin'=>$rq->input('password')]);
      $this->data=($rq->session()->has('client'))?$rq->session()->get('client'):$this->data;
      $this->products=($rq->session()->has('products'))?$rq->session()->get('products'):$this->products;
      //return redirect('client/info',['client'=>['fname'=>'asdas'$tw->fname,'mname'=>$tw->mname],'products'=>$this->products]);
      return redirect('client/info',['client'=>$this->data,'products'=>$this->products]);
    }
    /**
    * Protected section
    */
    protected $tw;
    protected $store=array();
    protected $name='Semeon Petrovicher';
    protected $status='lead';
    protected $data=array(
      'id'=>'00001'
      ,'fname'=>'Simeon'
      ,'sname'=>'Sisdov'
      ,'mname'=>'Petrovich'
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
