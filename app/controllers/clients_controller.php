<?php
class ClientsController extends AppController {
	
	var $helpers = array('Html','Ajax','Text','Time');
	
	var $components = array('RequestHandler');
	
	var $paginate = array(
		'limit' => 25,
		'order' => array(
			'Client.id' => 'desc'
		),
		'conditions' => array('Client.visible >' => 0)
	);
	
	function index() {
		$this->set('clients', $this->paginate('Client'));
	}
	
	function autoComplete()
	{
		$input = $this->data['Client']['input'];
		$users = $this->Client->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Client.nom_entier LIKE'=> '%'.$input.'%',
					'Client.adresse LIKE'	=> '%'.$input.'%',
					'Client.email LIKE'		=> '%'.$input.'%',
					'Client.telephone LIKE'	=> '%'.$input.'%',
				),
				'AND' => array('Client.visible >' => 0)
			),
			'limit' => 10,
			'fields' => array('id', 'nom', 'prenom')
		));
		$this->set(compact('input', 'users'));
	}

	
	function add() {
		if (!empty($this->data)) {
			$resultat = $this->Client->save( $this->data );
			if ($resultat) {
				$this->flash(
					'Le client vient d\'&ecirc;tre ajout&eacute;',
					$this->Session->read('App.redirect_to')
				);
			}
		}
	}
	
	function delete($id) {
		$this->Client->id = $id;
		$this->Client->saveField('visible', 0);
		
		
		
		$this->flash(
			'Le client a bien &eacute;t&eacute; effac&eacute;.',
			$this->Session->read('App.redirect_to')
		);
	}
	
	function view($id) {
		$this->Client->id = $id;
		$this->set('infos', $this->Client->read());
		$this->loadModel('Rdv');
		$this->set('rdvs', $this->Rdv->find('all', array('conditions' => array('Rdv.visible >' => 0, 'Rdv.client_id' => $this->Client->id))));
	}
	
	function edit($id=null) {
		if (empty($this->data)) {
			$this->Client->id = $id;
			$this->data = $this->Client->read();
		} else {
			$this->Client->id = $this->data['Client']['id'];
			if ($this->Client->save($this->data)) {
				
				$this->flash(
					'Le client a bien &eacute;t&eacute; modifi&eacute;',
					$this->Session->read('App.redirect_to')
				);
			}
		}
	}

}
?>
