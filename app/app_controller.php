<?php
class AppController extends Controller {

	var $components = array('Auth', 'Session');
	
	function beforeFilter() {
		$this->Auth->authError = "Vous devez vous connecter pour acc&eacute;der &agrave; cette partie du site";
		if ($this->referer() != '/') $redirect_to = $this->referer(); else $redirect_to = array('action' => 'index');
		$this->Session->write('App.redirect_to', $redirect_to);
	}
	
}
?>
