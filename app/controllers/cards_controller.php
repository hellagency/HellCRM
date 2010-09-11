<?php
class CardsController extends AppController {
	
	var $helpers = array('Html','Ajax','Text','Time');
	
	var $components = array('RequestHandler');
	
	var $paginate = array(
		'limit' => 25,
		'order' => array(
			'Card.id' => 'desc'
		),
		'conditions' => array('Card.visible >' => 0)
	);
	
	function index() {
		$this->set('cards', $this->paginate('Card'));
	}
	
	function create($client_id=null) {
		if (!empty($this->data)) {
			$resultat = $this->Card->save( $this->data );
			if ($resultat) {
				
				$this->flash(
					'La carte d\'abonnement vient d\'&ecirc;tre cr&eacute;e',
					$this->Session->read('App.redirect_to')
				);
			}
		}
		else
			$this->set('client_id', $client_id);
	}
	
	function delete($id) {
		$this->Card->id = $id;
		$this->Card->saveField('visible', 0);
		
		
		
		$this->flash(
			'La carte d\'abonnement a bien &eacute;t&eacute; effac&eacute;e',
			$this->Session->read('App.redirect_to')
		);
	}
	
	function edit($id=null) {
		if (empty($this->data)) {
			$this->Card->id = $id;
			$this->data = $this->Card->read();
		} else {
			$this->Card->id = $this->data['Card']['id'];
			if ($this->Card->save($this->data)) {
				
				$this->flash(
					'La carte d\'abonnement a bien &eacute;t&eacute; &eacute;dit&eacute;e',
					$this->Session->read('App.redirect_to')
				);
			}
		}
	}
}
?>