<?php
namespace mobi2\Adapters;

use Illuminate\Http\Request;

use  mobi2\Adapters\Adapter as RootAdapter;
use  mobi2\Adapters\VTBI\Adapter as VTBIAdapter;
use  mobi2\Adapters\FIMI\Adapter as FIMIAdapter;
use  mobi2\Adapters\OMS\Adapter as OMSAdapter;
use  mobi2\TymException;
use Logger;
class TranzWare extends RootAdapter{
    public function __construct(){
        $cfg=(defined(TRANZWARE_CONFIG_FILE))?TRANZWARE_CONFIG_FILE:dirname(__FILE__)."/../../config/tw.ini";
        $this->logger=Logger::getLogger(__CLASS__);
        $this->oms=new OMSAdapter($cfg);
        $this->fimi=new FIMIAdapter($cfg);
        $this->vtbi=new VTBIAdapter($cfg);
    }
    public function SetSession(Request $rq){
        $this->rq=$rq;
        if($this->rq->session()->has("KeyId"))$this->session["KeyId"]=$this->rq->session()->get("KeyId");
        if($this->rq->session()->has("telebank"))$this->session["telebank"]=$this->rq->session()->get("telebank");
        if($this->rq->session()->has("pinblock"))$this->session["pinblock"]=$this->rq->session()->get("pinblock");
        if($this->rq->session()->has("lastlogin"))$this->session["lastlogin"]=$this->rq->session()->get("lastlogin");
    }
    public function Register($arq){
        $this->checkRequestParameters($arq,array('fname','mname','sname','phone','email'),__METHOD__);
        $lead=$arq;
        $lead['login']=$lead['phone'];
        $lead['fio']=$lead['sname'].' '.$lead['fname'].' '.$lead['mname'];
        $lead['nameOnCard']=strtoupper($lead['fname'].' '.$lead['sname']);
        $lead['sex']='M';
        $lead['pin']=substr($lead['login'],strlen($lead['login'])-4);
        $res=array();
        try{
            $res=$this->oms->UserRegistration($lead);
            $lead['personid']=$res['personid'];
            $lead['account']=$res['account'];
            $res=$this->fimi->CreatePerson(['fio'=>$lead['fio'],'personid'=>$lead['personid'],'sex'=>$lead['sex']]);
            $res=$this->fimi->CreateAccount(['account'=>$lead['account'],'personid'=>$lead['personid']]);
            $res=$this->fimi->CreateVCard(['account'=>$lead['account'],'nameOnCard'=>$lead['nameOnCard']]);
            $lead['pan']=$res['pan'];
            $res=$this->fimi->CNSCardConfig(['pan'=>$lead['pan'],'phone'=>$lead['phone'],'personid'=>$lead['personid']]);
            $this->newsession();
            $res=$this->vtbi->CreateTBCustomer(['pin'=>$lead['pin'],'login'=>$lead['login'],'pan'=>$lead['pan'],'phone'=>$lead['phone'],'KeyId'=>$this->session['KeyId']]);
            $lead['telebank']=$res['telebank'];
            $res=$this->fimi->SetExtraAuthLevel(['telebank'=>$lead['telebank'],'level'=>'1']);
            $this->lastResponse=$lead;
            $this->session=$lead;
            $this->session['pinblock']=$lead['pin'];
            $this->rq->session()->put("telebank",$this->session['telebank']);
            $this->rq->session()->put("pinblock",$this->session['pinblock']);
            $this->lastAction=__FUNCTION__;
            return $lead;
        }
        catch(TymException $e){
            $this->logger->error($e->getMessage());
        }
        catch(Exception $e){
            $this->logger->error($e->getMessage());
        }
    }
    public function Login($arq){
        $this->newsession();
        $arq['KeyId']=$this->session['KeyId'];
        $this->gettelebank($arq);
        $arq['telebank']=$this->session['telebank'];
        $res=$this->vtbi->Logon($arq);
        $this->session['pinblock']=$res['pin'];
        $this->rq->session()->put("pinblock",$this->session['pinblock']);
        $this->session['lastlogin']=true;/// make timeouted
        $this->rq->session()->put("lastlogin",$this->session['lastlogin']);
        $this->lastResponse=$res;
        $this->lastAction=__FUNCTION__;
        return $res;
    }
    public function ChangePin($arq){
        $this->lastAction=__FUNCTION__;
        $this->debug(__METHOD__.$this->arrayToAttrString($this->session));
        try{
              $this->getlogin($arq);
              $arq['KeyId']=$this->session['KeyId'];
              $arq['telebank']=$this->session['telebank'];
              $this->lastResponse=$this->vtbi->PINChange($arq);
        }
        catch(TymException $e){
              if($e->getCode()==18){
                  $addr=['TranId'=>$e->getMessage()];
                  $addr=array_merge($addr,$arq,$this->vtbi->DynAuthAddressList($arq));
                  $dyn=$this->vtbi->GenerateDynPassword($addr);
                  $arq['needDynamicPassword']=1;
              }
              else throw $e;
        }
        return $arq;
    }
      public function GetRates($arq){
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $rs=$this->vtbi->GetCurrencyRates($arq);
          $res=[];
          $needRates=['991','810'];
          foreach($rs->Rates->Row as $row){
              if(($row->FromCurrency=='991')&&($row->ToCurrency=='810')){
                  $res['sell']=(array)$row;
              }
              if(($row->FromCurrency=='810')&&($row->ToCurrency=='991')){
                  $res['buy']=(array)$row;
              }
          }
          return $res;
      }
      public function Accounts($arq){
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $rs=$this->vtbi->Accts($arq);
          $res=[];
          foreach($rs->List->Row as $row){
              if(substr($row->Acct,5,3)=='991'){$res['pif']=(array)$row;$res['pif']['product']='pif';}
              else if(substr($row->Acct,0,5)=='40817'){$res['deb']=(array)$row;$res['deb']['product']='deb';}
              else if(substr($row->Acct,0,5)=='40903'){$res['pre']=(array)$row;$res['pre']['product']='pre';}
              else if(substr($row->Acct,0,4)=='4230'){$res['dep']=(array)$row;$res['dep']['product']='dep';}
              else $res[]=(array)$row;
          }
          return $res;
      }
      public function Logout($arq){
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $rs=$this->vtbi->Logout($arq);
          $res=[];
          if(isset($rs->List)){
              foreach($rs->List->Row as $row){
                  $res[]=(array)$row;
              }
          }
          return $res;
      }
      public function OperHistory($arq){
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $rs=$this->vtbi->OperHist($arq);
          $res=[];
          if(isset($rs->List)){
              foreach($rs->List->Row as $row){
                  $res[]=(array)$row;
              }
          }
          return $res;
      }
      public function History($arq){
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $rs=$this->vtbi->History($arq);
          $res=[];
          if(isset($rs->List)){
              foreach($rs->List->Row as $row){
                  $res[]=(array)$row;
              }
          }
          return $res;
      }
      public function Transfer($arq){
          $res=$arq;
          $arq['KeyId']=$this->session['KeyId'];
          $arq['telebank']=$this->session['telebank'];
          $arq['pinblock']=$this->session['pinblock'];
          $arq['Period']='0';
          $rs=$this->vtbi->Schedule($arq);
          return $res;
      }
      public function isLogin(){
          return isset($this->session['lastlogin'])&&$this->session['lastlogin'];
      }
  /**
   * Magic functions
   */
   public function __isset($name){
      if($name=="session")return true;
      return isset($this->session[$name]);
  }
  public function __get($name){
      if($name=="session")return $this->session;
      return isset($this->session[$name])?$this->session[$name]:false;
  }
  public function __set($name,$value){
      if($name!="session"){
          $this->session[$name]=$value;
      }
  }
  public function __unset($name){
      if($name!="session"){
          unset($this->session[$name]);
      }
  }
  /**
   * Protected section
   */
  protected function gettelebank($arq){
      if(isset($this->session['telebank'])&&strlen($this->session['telebank']))return true;
      $ars=$this->vtbi->GetPAN($arq);
      if(!isset($ars['telebank'])||!strlen($ars['telebank']))return false;
      $this->session['telebank']=$ars['telebank'];
      $this->rq->session()->put("telebank",$this->session['telebank']);
      return true;
  }
  protected function getsession(){
      if(!isset($this->session['KeyId'])||!strlen($this->session['KeyId'])){
          $this->newsession();
      }
      return true;
  }
  protected function newsession(){
      $ars=$this->vtbi->CreateSession([]);
      $this->session['KeyId']=$ars["KeyId"];
      $this->rq->session()->put("KeyId",$this->session['KeyId']);
      return true;
  }
  protected function getlogin($arq){
      if(!$this->isLogin()){
          $ars=$this->Login($arq);
      }
      return true;
  }
  protected $oms=null;
  protected $fimi=null;
  protected $vtbi=null;
  protected $rq;
  protected $lastResponse=array();
  protected $lastAction='MClient';
  protected $session=array();
}
?>
