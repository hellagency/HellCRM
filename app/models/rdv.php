<?php
class Rdv extends AppModel {

	var $belongsTo = array('Client');
	var $hasAndBelongsToMany  = array('Rdvcat');
	
	var $validate = array(
		'client_id' => array(
			'rule' => 'numeric',
			'message' => 'Entrez un chiffre.'
		),
		'textarea' => array(
			'rule' => array('between', 1, 255),
			'allowEmpty' =>  true,
			'message' => 'Doit comporter entre 1 et 255 caracteres.'
		)
	);
}
?>