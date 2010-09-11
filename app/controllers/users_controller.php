<?php
class UsersController extends AppController {
	
	var $components = array('Cookie', 'Session');
	
	function beforeFilter() {
		$this->Auth->autoRedirect = false;
		$this->Auth->loginError = "Mauvais identifiants de connexion, veuillez r&eacute;-essayer";
	}

	function login() {
		if ($this->Auth->user()) {
			if (!empty($this->data['User']['se_souvenir_de_moi'])) {
				$cookie = array(
					'username' => $this->data['User']['username'],
					'password' => $this->data['User']['password']
				);
				$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
				unset($this->data['User']['se_souvenir_de_moi']);
			}
			$this->redirect($this->Auth->redirect());
		}
		else if (empty($this->data)) {
			$cookie = $this->Cookie->read('Auth.User');
			if (!is_null($cookie)) {
				if ($this->Auth->login($cookie)) {
					$this->Session->delete('Message.auth');
					$this->redirect($this->Auth->redirect());
				}
			}
		}
    }

	function logout() {
		$cookie = $this->Cookie->read('Auth.User');
		if (!is_null($cookie)) {
			$this->Cookie->delete('Auth.User');
		}
        $this->redirect($this->Auth->logout());
    }
}
?>
