<?php

class DevinoSMS
{
    private $SessionID;
    private $soapClient;
    
    function __construct()
    {
        $this->soapClient = new SoapClient("http://ws.devinosms.com/SmsService.asmx?WSDL",array('exceptions' => 0)); 
        // Setup the RemoteFunction parameters
        $ap_param = array('login' =>  DEVINO_LOGIN,
                          'password' => DEVINO_PASSWORD); 

        $error = 0;
        
        do {
            $info = $this->soapClient->__soapCall("GetSessionID", array($ap_param));
            $cnt++;
        }while(is_soap_fault($info) && $cnt < 4);
        
        if ($cnt >= 3) {
             //print("ERROR: ".$fault->faultcode."-".$fault->faultstring);
             $this->SessionID = false;
             return false;
        } 

        $this->SessionID = $info->GetSessionIDResult;
        return true;
    }
    
    function Send($source,$phones,$message)
    {
        if (!$this->SessionID)  return false;
        
        $ap_param = array('sessionID' => $this->SessionID ,
                          'message' => array( 'Data' => $message,
                                              'SourceAddress' => $source,
                                              'ReceiptRequested' => false,
                                              'DestinationAddresses' => $phones )
                          ); 
      
        do {
            $info = $this->soapClient->__soapCall("SendMessage", array($ap_param));
            $cnt++;
        }while(is_soap_fault($info) && $cnt < 4);
        
        if ($cnt >= 3) {
             return false;
        } 
        
        return true;
    }
    
}

 
?>
