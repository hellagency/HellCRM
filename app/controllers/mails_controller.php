<?php
class MailsController extends AppController {
	
	var $helpers = array('Html','Ajax','Javascript', 'Time');
	
	var $components = array('Email','RequestHandler');
	
	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Mail.id' => 'desc'
		),
		'conditions' => array('Mail.visible >' => 0)
	);
	
	function index() {
		$this->set('mails', $this->paginate('Mail'));
	}
	
	function delete($id) {
		$this->Mail->id = $id;
		$this->Mail->saveField('visible', 0);
		
		$this->flash(
			'L\'email a &eacute;t&eacute; effac&eacute;.',
			$this->Session->read('App.redirect_to')
		);
	}
	
	function client($id=null) {
		if (empty($this->data)) {
			$this->set('id', $id);
		} else {
			$this->set('id', $this->data['Mail']['client_id']);
			if ($this->Mail->save($this->data)) {
				$this->loadModel('Client');
				$this->Client->id = $this->data['Mail']['client_id'];
				$receiver = $this->Client->field('email');
				Configure::load('email');
				$this->Email->from    = Configure::read('Email.senderName').' <'.Configure::read('Email.senderEmail').'>';
				$this->Email->to      = $receiver;
				$this->Email->subject = $this->data['Mail']['subject'];
				$this->Email->send($this->data['Mail']['textarea']);
				
				$this->flash(
					'Un email a bien &eacute;t&eacute; envoy&eacute; &agrave; '.$receiver,
					$this->Session->read('App.redirect_to')
				);
			}
		}
	}
	
	function allclients() {
		if (!empty($this->data)) {
			if ($this->Mail->save($this->data)) {
				$this->loadModel('Client');
				$emailsofclients = $this->Client->find('list', array('fields' => array('email'), 'conditions' => array('Client.visible >' => 0)));
				$nbr_sent = $nbr_unsent = $nbr_client = 0;
				foreach ($emailsofclients as $emailofclient)
				{
					if (!empty($emailofclient)) {
						Configure::load('email');
						$this->Email->from    = Configure::read('Email.senderName').' <'.Configure::read('Email.senderEmail').'>';
						$this->Email->to      = $emailofclient;
						$this->Email->subject = $this->data['Mail']['subject'];
						$this->Email->send($this->data['Mail']['textarea']);
						$nbr_sent++;
						$this->Email->reset();
					}
					else
						$nbr_unsent++;
						
					$nbr_client++;
				}
				
				
				$this->flash(
					$nbr_sent.' emails ont &eacute;t&eacute; envoy&eacute;s (soit '.$nbr_sent.'/'.$nbr_client.' clients)',
					$this->Session->read('App.redirect_to')
				);
			}
		}
	}
	
}
?>