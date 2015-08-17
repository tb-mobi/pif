<?php
namespace mobi2\Adapters\OMS;
use mobi2\Adapters\Adapter as RootAdapter;
use Logger;
class Adapter extends RootAdapter{
    public function __construct($cfg="config.ini"){
        $this->logger=Logger::getLogger(__CLASS__);
        $ini=parse_ini_file($cfg,true);
        $this->options['host']=isset($ini['OMS']['host'])?$ini['OMS']['host']:'http://127.0.0.1:1238';
    }
    public function ReserveAccount($arq){
        $acctType=3;
        if(isset($param['productCode'])&&$param['productCode']=='pif')$acctType=48; //49 кв метры
        $xml='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Header><ns2:RequestHeader xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><RType>Do</RType><Cpimode timeout="59">Sync</Cpimode><Branch>1</Branch><Station>50</Station></ns2:RequestHeader></soap:Header>';
        $xml.='<soap:Body><ns2:Request xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><Request>';
        $xml.='<UserDefinedCommand Id="GenAccountNo"><Command Action="Execute"></Command>';
        $xml.='<UserDefinedCommandParams Type="1">';
        $xml.='<Command Action="Init"/>';
        $xml.='<Param Id="ACCOUNTTYPE">4</Param>';
        $xml.='<Param Id="IDCLIENT">'.$arq['personid'].'</Param>';
        $xml.='</UserDefinedCommandParams></UserDefinedCommand>';
        $xml.='</Request></ns2:Request></soap:Body></soap:Envelope>';
        return $this->postData(['data'=>$xml]);
    }
    public function AccountCreate($param){
        $acctType=3;
        if(isset($param['productCode'])&&$param['productCode']=='pif')$acctType=48; //49 кв метры
        $xml='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Header><ns2:RequestHeader xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><RType>Do</RType><Cpimode timeout="59">Sync</Cpimode><Branch>1</Branch><Station>50</Station></ns2:RequestHeader></soap:Header>';
        $xml.='<soap:Body><ns2:Request xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><Request>';
        $xml.='<Account><Command Action="Create"/>';
		$xml.='<Type>3</Type>';
		$xml.='<CustomerId>'.$param['personid'].'</CustomerId>';
		$xml.='<LimitGrp>1</LimitGrp></Account>';
        $xml.='</Request></ns2:Request></soap:Body></soap:Envelope>';
        return $this->postData(['data'=>$xml]);
    }
    public function PersonCreate($param){
        $xml='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Header><ns2:RequestHeader xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><RType>Do</RType><Cpimode timeout="59">Sync</Cpimode><Branch>1</Branch><Station>50</Station></ns2:RequestHeader></soap:Header>';
        $xml.='<soap:Body><ns2:Request xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><Request>';
        $xml.='<PERSON>';
        $xml.='<Command Action="Create" ResObjectInfoType="FullInfo" />';
        $xml.='<NAME>'.$param['sname'].' '.$param['fname'].' '.$param['mname'].'</NAME>';
        $xml.='<SEX>М</SEX>';
        $xml.='<Phone>'.$param['phone'].'</Phone>';
        $xml.='<RESIDENT>TRUE</RESIDENT>';
        $xml.='<CUSTOMATTRIBUTES><ATTRIBUTE ID="IDENTITY">1</ATTRIBUTE></CUSTOMATTRIBUTES>';
        $xml.='</PERSON>';
        $xml.='</Request></ns2:Request></soap:Body></soap:Envelope>';
        return $this->postData(['data'=>$xml]);
    }
    public function UserRegistration($arq){
        $this->checkRequestParameters($arq,array('fio','phone','email'),__METHOD__);
        $xml='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Header><ns2:RequestHeader xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><RType>Do</RType><Cpimode timeout="59">Sync</Cpimode><Branch>1</Branch><Station>50</Station></ns2:RequestHeader></soap:Header>';
        $xml.='<soap:Body><ns2:Request xmlns:ns2="http://schemas.compassplus.com/twcms/1.0/omsi.xsd"><Request>';
        $xml.='<UserDefinedCommand Id="UserRegistration">';
        $xml.='<Command Action="Execute"></Command>';
        $xml.='<UserDefinedCommandParams Type="1">';
        $xml.='<Command Action="Init"/>';
        $xml.='<Param Id="Name">'.$arq['fio'].'</Param>';
        $xml.='<Param Id="Phone">'.$arq['phone'].'</Param>';
        $xml.='<Param Id="Email">'.$arq['email'].'</Param>';
        $xml.='</UserDefinedCommandParams>';
        $xml.='</UserDefinedCommand>';
        $xml.='</Request></ns2:Request></soap:Body></soap:Envelope>';
        $response=$this->postData(['data'=>$xml]);
        $nodes=$response->getElementsByTagName('PARAM');
        $res=array(
            'account'=>$this->searchIn(array('nodes'=>$nodes,'name'=>'ACCOUNTNO'))
            ,'personid'=>$this->searchIn(array('nodes'=>$nodes,'name'=>'PERSON'))
        );
        return $res;
    }

}
?>
