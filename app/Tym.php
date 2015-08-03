<?php
namespace mobi2;

class Tym{
  public function json(){
		if(is_null($this->_rawObject)){$this->logger->warn("response is null");return;}
		$this->_toStr=json_encode($this->_rawObject);
		$this->logger->debug($this->_toStr);
		return $this->_toStr;
	}
	public function xml(){
		if(is_null($this->_rawObject)){$this->warn("response is null");return;}
		$xml = new SimpleXMLElement();
		array_walk_recursive($this->_rawObject, array ($xml));
		$this->_toStr=$xml->asXML();
		$this->debug($this->_toStr);
		return $this->_toStr;
	}
	public static function toXml($arr,$root='Envelope'){
		if(is_null($this->_rawObject)||!is_array($arr)){$this->warn("input object {$arr} is null");return;}
		$xml = new SimpleXMLElement();
		array_walk_recursive($this->_rawObject, array ($xml));
		$this->_toStr=$xml->asXML();
		$this->debug($this->_toStr);
		return $this->_toStr;
	}
	public static function StrtoHex($str=""){
		$res="";
		for($i=0;$i<strlen($str);$i++){
			$chr=ord($str[$i]);
			$chrh=strtoupper(dechex((int)$chr));
			$chrh=(strlen($chrh)<2?'0':'').$chrh;
			$res.=$chrh;
		}
		return $res;
	}
	public static function HexToStr($str=""){
		$res="";
		for($i=0;$i<strlen($str);$i+=2){
			$res.=chr(hexdec($str[$i].$str[$i+1]));
		}
		return $res;
	}
  public function __get($n){
      $accessed=['lastResponse','lastAction'];
      if(in_array($n,$accessed)){return $this->$n;}
    else if(property_exists(get_called_class(),"_user_data")){
      return isset($this->_user_data[$n])?$this->_user_data[$n]:null;
    }

  }
  public function _get(){
    //return $this->object2Array($this);
    $res=array();
    if(property_exists(get_called_class(),"_list")){
      foreach($this->_list as $item){
        array_push($res,$item->get());
      }
      return $res;
    }
    foreach(get_class_vars(get_called_class()) as $p=>$v){
      if(is_object($this->$p)){
        if(class_exists(get_class($this->$p)))$res[$p]=$this->$p->_get();//other object of TymLib
      }
      else if(is_array($this->$p)){
        $arr=$this->$p;
        if(isset($arr["year"])&&isset($arr["month"]))
          $res[$p]=$arr["year"].'-'.$arr["mon"].'-'.$arr["mday"].'T'.$arr["hours"].':'.$arr["minutes"].':'.$arr["seconds"].'.99'.date('O');//datetime object
      }
      else $res[$p]=$this->$p;
    }
    return $res;
  }
  protected $options=array(
    'host'=>'http://localhost:1238'
    ,'timeout'=>24
    ,'connectiontimeout'=>16
    ,'station'=>50
		,'trace'=>0
  );
  protected $requestHeaders=array(
		"Ver"=>"4"
		,"Product"=>"TB"
		,'STAN'=>0
    ,'RetAddress'=>'172.17.31.25'
	);
  protected $curlObj;
  protected $_rawObject;
  protected $logger;
    protected function info($str){
		$this->logger->info($str);
	}
	protected function debug($str){
		$this->logger->debug($str);
	}
	protected function warn($str){
		$this->logger->warn($str);
	}
	protected function error($str,$ex){
		$this->logger->error($str,$ex);
	}
	protected function checkRequestParameters(&$arq,$needed,$func=__METHOD__){
		$this->info($func.'['.$this->arrayToAttrString($arq).']');
        if(isset($arq['login'])){$arq['login']=preg_replace('/\+*/','',$arq['login']);}
		foreach($needed as $need){
			if(!isset($arq[$need])){
				$e=new Exception('Parameter '.$need.' is requered for '.$func);
				$this->error($e->getMessage(),$e);
				throw $e;
			}
		}
		return true;
	}
	protected function arrayToAttrString($arr){
		$res="";
		foreach($arr as $k=>$v){
			if($k=="STAN"){
				$v+=1;
				$this->requestHeaders['STAN']+=1;
			}
			$res.=" {$k}=\"{$v}\"";
		}
		return $res;
	}

	protected function postData($params){
        $this->debug('REQUEST:'.$params['data']);
        $s=curl_init();
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"Request\"",
            "Content-length: ".strlen($params['data'])
        );
        curl_setopt($s,CURLOPT_URL,$this->options['host']);
        curl_setopt($s,CURLOPT_TIMEOUT,$this->options['timeout']);
        curl_setopt($s,CURLOPT_CONNECTTIMEOUT,$this->options['connectiontimeout']);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($s,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($s,CURLOPT_POST,true);
        curl_setopt($s,CURLOPT_POSTFIELDS,$params['data']);
        curl_setopt($s,CURLOPT_VERBOSE, $this->options['trace']);
        $this->response=curl_exec($s);
        if(curl_errno($s)==CURLE_COULDNT_CONNECT){
            throw new TymException('Couldnt connect.');
        }
        $status = curl_getinfo($s,CURLINFO_HTTP_CODE);
        $err = curl_error($s);
        curl_close($s);
        $this->debug('RESPONSE:'.$this->response."\n");
        $this->readFault($this->response);
        return $this->parseResponse($this->response);
    }
	protected function readFault($str){
		$fault=new DOMDocument('1.0','utf-8');
        $fault->loadXML($str);
		if($fault->getElementsByTagName('Fault')->length){
			list($code,$message)=array(-1,'');
			$code=$this->searchIn(array('nodes'=>$fault->getElementsByTagName('Value'),'name'=>'Value','type'=>'element'));
			$message=$this->searchIn(array('nodes'=>$fault->getElementsByTagName('Text'),'name'=>'Text','type'=>'element'));
            try{
                $message=strlen($message)?$message:$this->searchIn(array('nodes'=>$fault->getElementsByTagName('TranId'),'name'=>'TranId','type'=>'element'));
            }
            catch(TymException $e){}
			$code=intval($code)?intval($code):-1;
			$e=new TymException($message,$code);
			$this->error('['.$code.']'.$message,$e);
			throw $e;
		}
	}
	protected function parseResponse($rsp){
        $dom=new DOMDocument('1.0','utf-8');
        $dom->loadXML($rsp);
		return $dom;
        $res=$this->DOM2Array($dom);
		return $res;
	}
    protected function searchIn($pars){
		if(!isset($pars['nodes'])){
			$e=new TymException('No node list, cant find parameter.');
			$this->error('No node list, cant find parameter.',$e);
			throw $e;
		}
			if(!isset($pars['name'])){
				$e=new Exception('No name, cant find parameter.');
				$this->error('No name, cant find parameter.',$e);
				throw $e;
			}
		$nodes=$pars['nodes'];
		$name=$pars['name'];
		$type=isset($pars['type'])?$pars['type']:"byid";
		for($i=0;$i<$nodes->length;++$i){
			$node=$nodes->item($i);
			//var_dump($node->attributes->getNamedItem("ID"));
			if($type==="element"){
				if($node->nodeType==XML_ELEMENT_NODE)return $node->nodeValue;
			}
			else if($type==="byid"){
				if($node->hasAttributes()
					&&$node->attributes->getNamedItem("ID")!=null
					&&$node->attributes->getNamedItem("ID")->nodeValue==$name
					){
					return $node->nodeValue;
				}
			}
		}
		$e=new Exception('Parameter '.$name.' not found.');
		$this->error($e->getMessage(),$e);
		throw $e;
	}
    protected function DOM2Array($root,$strick_ns=true){
        $result = array();
        $root=(get_class($root)==="DOMDocument"&&$root->getElementsByTagName('Body')->length)?$root->getElementsByTagName('Body')->item(0):$root;
        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['#attributes'][$attr->name] = $attr->value;
            }
        }
        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['#value'] = $child->nodeValue;
                    return count($result) == 1
                        ? $result['#value']
                        : $result;
                    }
                }
                $groups = array();
                foreach ($children as $child) {
                    $nodeName=$strick_ns?preg_replace('/(\S+):(\S+)/i','$2',$child->nodeName):$child->nodeName;
                    if (!isset($result[$nodeName])) {
                        $result[$nodeName] = $this->DOM2Array($child);
                    } else {
                        if (!isset($groups[$nodeName])) {
                            $result[$nodeName] = array($result[$nodeName]);
                            $groups[$nodeName] = 1;
                        }
                        $result[$nodeName][] = $this->DOM2Array($child);
                    }
                }
            }
            return $result;
    }
    protected function searchInDOMArray($arq){
        foreach($arq['dom'] as $node){

        }
    }
    public function des($key,$value,$hex=true){
        $this->info("DES\tkey=".$key."\t[".(strlen($key)/2)."] {".pack('H*',$key)."}");
        $this->info("DES\tvalue=".$value."\t[".(strlen($value)/2)."] {".pack('H*',$value)."}");
        $key=pack('H*',$key);
        $value=pack('H*',$value);
        $res=mcrypt_encrypt(MCRYPT_DES, $key, $value, MCRYPT_MODE_CBC,str_repeat("\0",8));
        $this->info("DES\tres=".Tym::StrToHex($res)."\t".$res);
        return Tym::StrToHex($res);
    }
    public function doubledes($key,$value,$hex=true){
        $this->info("2DES\tkey=".$key."\t[".(strlen($key))."] {"      .join('',unpack('H*',$key))."}");
        $this->info("2DES\tvalue=".$value."\t[".(strlen($value))."] {".join('',unpack('H*',$value))."}");
        $iv = str_repeat("\0",8);
        $ciphers=openssl_get_cipher_methods();
        //foreach($ciphers as $cipher){
        //    if(preg_match('/.*des.*/i',$cipher)){
        //        $res=openssl_encrypt($value,$cipher,$key,OPENSSL_RAW_DATA);
        //        $this->info($cipher."\tres=".Tym::StrToHex($res)."\t".$res);
        //    }
        //}
        $res=openssl_encrypt($value,'des-ede',$key,OPENSSL_RAW_DATA);
        $res=substr($res,0,strlen($res)/2);
        $this->info("2DES\tres=".Tym::StrToHex($res)."\t".$res);
        return Tym::StrToHex($res);
    }
    public function tripledes($key,$value,$hex=true){
        $this->info("3DES\tkey=".$key."\t[".(strlen($key)/2)."] {".pack('H*',$key)."}");
        $this->info("3DES\tvalue=".$value."\t[".(strlen($value)/2)."] {".pack('H*',$value)."}");
        $key=pack('H*',$key);
        $value=pack('H*',$value);
        $iv=str_repeat("\0",8);
        $res=mcrypt_encrypt(MCRYPT_3DES, $key, $value, MCRYPT_MODE_CBC,str_repeat("\0",8));
        $this->info("3DES\tres=".Tym::StrToHex($res)."\t".$res);
        return Tym::StrToHex($res);
    }
    public function des2($key,$value,$hex=true){
        $this->info('key='.Tym::StrToHex($key)."\t[".strlen($key)."]".$key);
        $this->info('value='.Tym::StrToHex($value)."\t[".strlen($value)."]".$value);
        //$key=substr($key,0,8);
        $iv=str_repeat("\0",8);
        $res=mcrypt_encrypt(MCRYPT_DES, $key, $value, MCRYPT_MODE_ECB,$iv);
        $this->info('res='.Tym::StrToHex($res)."\t".$res);
        $res=$hex?strtoupper(Tym::StrToHex($res)):$res;
        return $key;
    }
};
?>
