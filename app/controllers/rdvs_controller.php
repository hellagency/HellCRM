<?php
class RdvsController extends AppController {
	
	var $helpers = array('Html','Ajax','Text','Time');
	
	var $components = array('RequestHandler');
	
	var $uses = array('Rdv','Client');
	
	var $paginate = array(
		'limit' => 25,
		'order' => array(
			'Rdv.id' => 'desc'
		),
		'conditions' => array('Rdv.visible >' => 0)
	);
	
	function index() {
		$this->set('rdvs', $this->paginate('Rdv'));
	}
	
	function add($client_id=null) {
		if (!empty($this->data)) 
		{
			if ($this->Rdv->save( $this->data )) {
				
				$this->flash(
					'Le RDV vient d\'&ecirc;tre ajout&eacute;',
					$this->Session->read('App.redirect_to')
				);
			}
		}
		$rdvcats = $this->Rdv->Rdvcat->find('list', array('conditions' => array('Rdvcat.visible >' => 0)));
		$this->set(compact('client_id', 'rdvcats'));
	}
	
	function autoComplete()
	{
		$input = $this->data['Rdv']['client_id'];
		$users = $this->Client->find('all', array(
			'conditions' => array(
				'AND' => array(
					'Client.nom_entier LIKE'	=> '%'.$input.'%',
					'Client.visible >' => 0
				)
			),
			'limit' => 10,
			'fields' => array('id', 'nom', 'prenom')
		));
		$this->set(compact('input', 'users'));
	}
	
	function edit($id=null) {
		if (empty($this->data)) 
		{
			$this->Rdv->id = $id;
			$this->data = $this->Rdv->read();
		}
		else {
			$this->Rdv->id = $this->data['Rdv']['id'];
			if ($this->Rdv->save($this->data)) {
				
				$this->flash(
					'Le RDV vient d\'&ecirc;tre &eacute;dit&eacute;',
					$this->Session->read('App.redirect_to')
				);
			}
		} 
		$this->set('rdvcats', $this->Rdv->Rdvcat->find('list', array('conditions' => array('Rdvcat.visible >' => 0))));
	}
	
	function delete($id) {
		$this->Rdv->id = $id;
		$this->Rdv->saveField('visible', 0);
		
		
		$this->flash(
			'Le RDV a bien &eacute;t&eacute; effac&eacute;.',
			$this->Session->read('App.redirect_to')
		);
	}
}
?>