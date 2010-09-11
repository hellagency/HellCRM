<?php
class RdvcatsController extends AppController {

	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Rdvcat.id' => 'desc'
		),
		'conditions' => array('Rdvcat.visible >' => 0)
	);
	
	function index() {
		$this->set('cats', $this->paginate('Rdvcat'));
	}
	
	function delete($id) {
		$this->Rdvcat->id = $id;
		$this->Rdvcat->saveField('visible', 0);
		
		$this->flash(
			'La cat&eacute;gorie a &eacute;t&eacute; effac&eacute;.',
			$this->Session->read('App.redirect_to')
		);
	}
}
?>