<?php
namespace mobi2\Adapters\VTBI;
use mobi2\Adapters\Adapter as RootAdapter;
use Logger;
class Adapter extends RootAdapter{
  public function __construct($cfg="config.ini"){
      $this->logger=Logger::getLogger('VTBIAdapter');
      $ini=parse_ini_file($cfg,true);
      $this->options['host']=isset($ini['VTBI']['host'])?$ini['VTBI']['host']:'http://127.0.0.1:15003';
      $this->options['station']=isset($ini['VTBI']['station'])?$ini['VTBI']['station']:'50';
      $this->options['encrypt']=isset($ini['VTBI']['encrypt'])?$ini['VTBI']['encrypt']:0;
      $this->options['schemans']=isset($ini['VTBI']['schemans'])?$ini['VTBI']['schemans']:'http://schemas.compassplus.com/two/1.0/telebank.xsd';
      $this->requestHeaders=array(
    		"Ver"=>"4"
    		,"Product"=>"TB"
    	);
  }
  public function GetKey(){
    $p=$this->makeVTBIRequest(__FUNCTION__);
    $response=$this->postData($p);
    $res=array(
        'KeyId'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','KeyId')->item(0)->nodeValue,
        'Key'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','Key')->item(0)->nodeValue
    );
    $this->requestHeaders['KeyId']=$res['KeyId'];
    $this->sessionKey=$res['Key'];
    return $res;
  }
  public function CreateSession(){
    $p=$this->makeVTBIRequest(__FUNCTION__);
    $response=$this->postData($p);
    $res=array(
        'KeyId'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','KeyId')->item(0)->nodeValue
    );
    $this->requestHeaders['KeyId']=$res['KeyId'];
    return $res;
  }
  public function GetPAN($arq){
    $this->checkRequestParameters($arq,array('login'),__METHOD__);
    $p=$this->makeVTBIRequest(__FUNCTION__,array('TextLogin'=>$arq['login']));
    $response=$this->postData($p);
    $res=array(
        'telebank'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','PAN')->item(0)->nodeValue
    );
    return $res;
  }
  public function ChangeTextLogin($arq){
    $this->checkRequestParameters($arq,array('login'),__METHOD__);
    $p=$this->makeVTBIRequest(__FUNCTION__,array('TextLogin'=>$arq['login']));
    $res=$this->postData($p);
    return $res;
  }
  public function Logon($arq){
    $this->checkRequestParameters($arq,array('pin','telebank'),__METHOD__);
    $pin=$arq['pin'];
    $this->requestHeaders['PIN']=$pin;
    $this->requestHeaders['PAN']=$arq['telebank'];
    $this->requestHeaders['MBR']=0;
    //$p=$this->makeVTBIRequest(__FUNCTION__,array('TextLogin'=>$login));
    $p=$this->makeVTBIRequest(__FUNCTION__,array());
    $dom=$this->postData($p);
    if($dom->getElementsByTagName('Response')->length){
        $response=$dom->getElementsByTagName('Response')->item(0);
        $xmls=$dom->saveXML($response,LIBXML_NOEMPTYTAG);
        $xmls=preg_replace(['/\<\/(\S+?):/im','/\<(\S+?):/im'],['</','<'],$xmls);
        $sxe=simplexml_load_string($xmls,'SimpleXMLElement',LIBXML_NOBLANKS);
        $res=(array)$sxe;
    }
    $res=array(
        'fio'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','Name')->item(0)->nodeValue
        ,'pin'=>$pin
    );
    return $res;
  }
  public function CreateTBCustomer($arq){
    $this->checkRequestParameters($arq,array('pin','login','phone','pan'),__METHOD__);

    $this->requestHeaders['AuthPAN']=$arq['pan'];
    $this->requestHeaders['AuthMBR']='0';
    $this->requestHeaders['PIN']=substr($arq['pan'],strlen($arq['pan'])-13,12);
    //$p=$this->makeVTBIRequest(__FUNCTION__,array('TextLogin'=>$login));
    $p=$this->makeVTBIRequest(__FUNCTION__,array(
      'TBCustomerPIN'=>$arq['pin']
      ,'TextLogin'=>preg_replace('/\+/i','',$arq['login'])
      ,'Address'=>preg_replace('/\+/i','',$arq['phone'])
    ));
    $res=array();
    try{
        $response=$this->postData($p);
        $res=array(
            'telebank'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','PAN')->item(0)->nodeValue
        );
    }catch(TymException $e){
      if(preg_match('/text login "'.$arq['login'].'" already exists in institution .*/i',$e->getMessage())){
        $this->warn('Already client');
      }else throw $e;
    }
    return $res;
  }
  public function SetExtraAuthLevel($arq){
      $this->checkRequestParameters($arq,array('pin','telebank'),__METHOD__);

      $this->requestHeaders['PIN']=$arq['pin'];
      $this->requestHeaders['PAN']=$arq['telebank'];
      $this->requestHeaders['MBR']=0;
      $level=isset($arq['level'])?$arq['level']:'1';
      $p=$this->makeVTBIRequest(__FUNCTION__,['NewLevel'=>$level]);
      $response=$this->postData($p);
      return $arq;
  }
  public function PINChange($arq){
    $this->checkRequestParameters($arq,array('pin','newpin','telebank'),__METHOD__);
    $this->requestHeaders['PIN']=$arq['pin'];
    $this->requestHeaders['PAN']=$arq['telebank'];
    $this->requestHeaders['MBR']=0;
    if(isset($arq['DynamicPassword'])){
        $pass=$arq['DynamicPassword'];
        $password=$pass;
        $passhash=$this->doubledes(pack('A16',strtoupper($password)),pack('A8',strtoupper($password)));
        //$passhash=$this->des($passhash,Tym::StrToHex($pass));
        $this->requestHeaders['DynamicPassword']=$passhash;
        //return $arq;
    }
    $p=$this->makeVTBIRequest(__FUNCTION__,array('NewPIN'=>$arq['newpin']));
    $response=$this->postData($p);
    return $arq;
  }
  public function GenerateDynPassword($arq){
      $this->checkRequestParameters($arq,array('TranId','address','channel','telebank','pin'),__METHOD__);
      $this->requestHeaders['PrevTranId']=$arq['TranId'];
      $this->requestHeaders['PIN']=$arq['pin'];
      $this->requestHeaders['PAN']=$arq['telebank'];
      $this->requestHeaders['MBR']='0';
      $p=$this->makeVTBIRequest(__FUNCTION__,['Channel'=>$arq['channel'],'Address'=>$arq['address']]);
      $response=$this->postData($p);
      $res=array(
          'passCount'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','PasswordCount')->item(0)->nodeValue,
          'LifeTime'=>$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','LifeTime')->item(0)->nodeValue
      );
      return $res;
  }
  public function DynAuthAddressList($arq){
      $p=$this->makeVTBIRequest(__FUNCTION__);
      $response=$this->postData($p);
      $row=$response->getElementsByTagNameNS('http://schemas.compassplus.com/two/1.0/telebank.xsd','Row');
      $dynAddress="";
      $channel="";
      for($i=0;$i<$row->length;++$i){
          $item=$row->item($i);
          $default=0;
          foreach($item->childNodes as $child){
              $nodeName=preg_replace('/(\S+):(\S+)/i','$2',$child->nodeName);
              parent::info($nodeName.'='.$child->nodeValue);
              $dynAddress=($nodeName=="Address")?$child->nodeValue:$dynAddress;
              $channel=($nodeName=="Channel")?$child->nodeValue:$channel;
              $default=($nodeName=="Default")?$child->nodeValue:0;
          }
          if($default==1)break;
      }
      $res=array(
          'address'=>$dynAddress,
          'channel'=>$channel
      );
      return $res;
  }
  public function ChangePassword($pass){
    $newPin=$pass;
    $pan=$this->requestHeaders['PAN'];
    $newPin=$this->passowrdHash($pan,$newPin);
    $this->requestHeaders['PIN']="";
    $this->debug('newPIN='.$newPin);
    $p=$this->makeVTBIRequest(__FUNCTION__,array('NewPassword'=>$newPin));
    $res=$this->postData($p);
    return $res;
  }
  protected $sessionKey="";
  protected $sessionKeyId="";
  protected $login="test";
  protected function makeVTBIRequest($f,$p=null){
      $res=array('func'=>$f,'data'=>'<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tel="http://schemas.compassplus.com/two/1.0/telebank.xsd"><soap:Header/><soap:Body>');
      $res['data'].="\n\t<tel:{$f}Rq>\n\t\t";
      $res['data'].='<tel:Request'.$this->arrayToAttrString($this->requestHeaders);
      if(!is_null($p)&&count($p)){
          $res['data'].=">";
          foreach($p as $k=>$v){
              $res['data'].="\n\t\t\t<tel:{$k}>{$v}</tel:{$k}>";
          }
          $res['data'].="\n\t\t</tel:Request>";
      }else $res['data'].="/>";
      $res['data'].="\n\t</tel:{$f}Rq>";
      $res['data'].='</soap:Body></soap:Envelope>';
      return $res;
  }
  protected function passowrdHash($pan,$pin){
    $res=$pan.$pin;
    $res=sha1($res);
    $res=substr($res,0,16);
    $res=strtoupper($res);
    return $res;
  }
  protected function pinBlock($key,$pan,$pin){
    $this->debug('PIN block start.');
    if(!$this->options['encrypt']){
      return Tym::StrToHex($pin);
    }
    $pinLen=strlen($pin);
    $pinStr=((strlen($pin)<10)?'0':'').$pinLen.$pin.str_repeat('F',14-strlen($pin));
    $this->debug('PIN['.$pin.']str='.$pinStr);
    $panStr='0000'.substr($pan,strlen($pan)-13,12);
    $this->debug('PAN['.$pan.']str='.$panStr);
    $xored=$this->xorStr($pinStr,$panStr);
    $this->debug('XORed='.$xored);
    $this->debug('XORedHEX='.Tym::StrToHex($xored));
    $res=$this->desEncrypt($key,$xored);
    $this->debug('PINBlock='.$res);
    $this->debug('PIN block end.');
    return $res;
  }
  protected function xorStr($str1,$str2){
    // Let's define our key here
     $key =pack('H*',$str1);
     // Our plaintext/ciphertext
     $text = pack('H*',$str2);
     // Our output text
     $outText = '';
     // Iterate through each character
     for($i=0;$i<strlen($text);){
         for($j=0;($j<strlen($key) && $i<strlen($text));$j++,$i++){
             $outText .= $text{$i} ^ $key{$j};
         }
     }
     $outText=strtoupper($outText);
     return $outText;
  }
  protected function desEncrypt($key,$data){
    $res="";
    $iv_size=mcrypt_get_iv_size("tripledes","ebc");
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    //$key=Tym::HexToStr($key);
    $key=pack('H*',$key);
    $key=$key.substr($key,0,(24-strlen($key)));
    $this->debug('keyStr='.$key);
    $res=mcrypt_encrypt(MCRYPT_3DES, $key, $data, MCRYPT_MODE_ECB);
    $res=strtoupper(Tym::StrToHex($res));
    return $res;
  }
  protected function checkSession($arq){
    if(!isset($this->requestHeaders['KeyId'])||!strlen($this->requestHeaders['KeyId'])){
      $this->options['encrypt']?
        $this->GetKey()
        :$this->CreateSession();
    }
    //if(!isset($this->requestHeaders['PAN'])||!strlen($this->requestHeaders['PAN']))$this->GetPan($arq);
  }
}
?>
