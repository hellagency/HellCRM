<?php
class OVHSoapComponent extends Object {

	var $components = array('Session');
	
	var $Controller = null;

    function initialize(&$Controller) {
		if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
			exit ("OVHSoap : Vous ne pouvez pas envoyer de SMS &agrave; partir d'un serveur local");
		}
        $this->controller =& $Controller;
		Configure::load('o_v_h_soap');
		$this->client = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.11.wsdl");
		$session = $this->client->login(Configure::read('OVHSoap.nic'), Configure::read('OVHSoap.password'),Configure::read('OVHSoap.lang'), false);
		$this->Session->write('OVHSoap.session', $session);
	} 
	
	function telephonySmsCreditLeft() {
		$session = $this->Session->read('OVHSoap.session');
		try { 
			return $this->client->telephonySmsCreditLeft($session, Configure::read('OVHSoap.smsAccount'));
		}
        catch (SoapFault $e) {
            return $e; 
        } 
	}
	
	function telephonySmsSend($numberTo, $message, $smsValidity=2880, $smsClass=1, $smsDeferred=0, $smsPriority=3) {
		$session = $this->Session->read('OVHSoap.session');
		try { 
			return $this->client->telephonySmsSend($session, Configure::read('OVHSoap.smsAccount'), Configure::read('OVHSoap.numberFrom'), $numberTo, $message, $smsValidity, $smsClass, $smsDeferred, $smsPriority);
		}
        catch (SoapFault $e) {
            return $e; 
        }
	}
}

?>
