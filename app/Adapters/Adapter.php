<?php
namespace mobi2\Adapters;
use mobi2\TymException;
use mobi2\Tym;
use \DOMDocument;
class Adapter{
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
    public function __call($func,$args){
        if(method_exists($this,$func)){
            return call_user_func_array($this->$func,$args);
        }
        $arq=isset($args[0])?$args[0]:[];
        $this->checkRequestParameters($arq,[],__METHOD__);
        foreach($arq as $name=>$value){
            if(in_array($name,['telebank','pinblock','KeyId']))unset($arq[$name]);
        }
        $p=$this->makeRequest($func,$arq);
        $dom=$this->postData($p);
        if($dom->getElementsByTagName('Response')->length){
            $response=$dom->getElementsByTagName('Response')->item(0);
            $xmls=$dom->saveXML($response,LIBXML_NOEMPTYTAG);
            $xmls=preg_replace(['/\<\/(\S+?):/im','/\<(\S+?):/im'],['</','<'],$xmls);
            $sxe=simplexml_load_string($xmls,'SimpleXMLElement',LIBXML_NOBLANKS);
            return $sxe;
        }
        return $dom;
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
    protected $options=array(
        'host'=>'http://localhost:1238'
        ,'timeout'=>24
        ,'connectiontimeout'=>16
        ,'station'=>50
		,'trace'=>0
        ,'schemans'=>'http://schemas.compassplus.com/two/1.0/telebank.xsd'
    );
    protected $requestHeaders=array(
		"Ver"=>"4"
		,"Product"=>"TB"
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
        if(isset($arq['login'])){$arq['login']=preg_replace('/[\+\s\(\)\-]*/m','',$arq['login']);}
        if(isset($arq['telebank']))$this->requestHeaders['PAN']=$arq['telebank'];
        if(isset($arq['pinblock']))$this->requestHeaders['PIN']=$arq['pinblock'];
        if(isset($arq['KeyId']))$this->requestHeaders['KeyId']=$arq['KeyId'];
		foreach($needed as $need){
			if(!isset($arq[$need])){
				$e=new TymException('Parameter '.$need.' is requered for '.$func);
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
    protected function makeRequest($f,$p=null){
        $res=array('func'=>$f,'data'=>'<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tel="'.$this->options['schemans'].'"><soap:Header/><soap:Body>');
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
            if(in_array($code,[18,1022])){
                $message=strlen($message)?$message:$this->searchIn(array('nodes'=>$fault->getElementsByTagName('TranId'),'name'=>'TranId','type'=>'element'));
            }
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
	}
    protected function searchIn($pars){
		if(!isset($pars['nodes'])){
			$e=new TymException('No node list, cant find parameter.');
			$this->error('No node list, cant find parameter.',$e);
			throw $e;
		}
			if(!isset($pars['name'])){
				$e=new \Exception('No name, cant find parameter.');
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
		$e=new \TymException('Parameter '.$name.' not found.');
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
