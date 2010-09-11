<?php
if  ($session->check('Message.auth')) e($session->flash('auth'));
e($form->create('User', array('action' => 'login')));
e($form->inputs(array(
	'legend' => __('Identification', true),
	'username' => array('label' => 'Nom d\'utilisateur' ),
	'password' => array('label' => 'Mot de passe' ),
	'se_souvenir_de_moi' => array('label' => 'Se souvenir de moi', 'type' => 'checkbox' )
)));
e($form->end('Identifier'));
?>