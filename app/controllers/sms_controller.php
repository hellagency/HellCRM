<?php
class SmsController extends AppController {
	
	var $helpers = array('Html','Ajax','Javascript');
	
	var $components = array('RequestHandler', 'OVHSoap');
	
	var $uses = array('Sms', 'Client');
	
	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Sms.id' => 'desc'
		),
		'conditions' => array('Sms.visible >' => 0)
	);
	
	function index() {
		$this->set('sms', $this->paginate('Sms'));
		$this->set('credit', $this->OVHSoap->telephonySmsCreditLeft());
	}
	
	function delete($id) {
		$this->Sms->id = $id;
		$this->Sms->saveField('visible', 0);
		
		$this->flash(
			'Le sms a &eacute;t&eacute; effac&eacute;.',
			$this->Session->read('App.redirect_to')
		);
	}
	
	function client($id=null) {
		$credit = $this->OVHSoap->telephonySmsCreditLeft();	
		if ($credit==0)
		{
			$this->flash(
				'Vous avez &eacute;puis&eacute; votre cr&eacute;dit de sms pr&eacute;-pay&eacute;s, merci de le recharger.',
				array('controller'=> 'sms', 'action'=>'reload')
			);
		}
		else {
			$this->set('credit_after', $credit-1);
		
			if (empty($this->data)) {
				$this->set('id', $id);
			} else {
				$this->set('id', $this->data['Sms']['client_id']);
				if ($this->Sms->save($this->data)) {
					$this->Client->id = $this->data['Sms']['client_id'];
					$receiver = $this->Client->read('telephone');
					$sms_id = $this->OVHSoap->telephonySmsSend($receiver['Client']['telephone'], $this->data['Sms']['textarea']);
					
					$this->flash(
						'Un sms #'.$sms_id.' a bien &eacute;t&eacute; envoy&eacute; au '.$receiver['Client']['telephone'],
						$this->Session->read('App.redirect_to')
					);
				}
			}
		}
	}
	
	function allclients() {
		$credit = $this->OVHSoap->telephonySmsCreditLeft();
		$clients = $this->Client->find('list', array('fields' => array('telephone')));
		$numberofsmstosend = 0;
		foreach ($clients as $client) 
		{
			if (!empty($client)) 
				$numberofsmstosend++;
		}
		if ($credit < $numberofsmstosend)
		{
			$this->flash(
				'Vous avez '.$credit.' sms pr&eacute;-pay&eacute;s, or vous avez '.$numberofsmstosend.' sms a envoyer. Merci de recharcher votre cr&eacute;dit',
				array('controller'=> 'sms', 'action'=>'reload')
			);
		}
		else {
			if (empty($this->data)) {
				$this->set('numberofsmstosend', $numberofsmstosend);
				$this->set('credit_after', $credit-$numberofsmstosend);
			}
			else {
				if ($this->Mail->save($this->data)) {
					$receivers = $this->Client->find('list', array('fields' => array('telephone')));
					foreach ($receivers as $receiver)
						$sms_ids .= $this->OVHSoap->telephonySmsSend($receiver, $this->data['Sms']['textarea']).', ';
					
					$this->flash(
						'Plusieurs sms ont bien &eacute;t&eacute; envoy&eacute;s :<br/>'.$sms_ids,
						$this->Session->read('App.redirect_to')
					);
				}
			}	
		}
	}
}
?>