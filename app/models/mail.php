<?php
class Mail extends AppModel {

	var $belongsTo  = array('Client');
	
	var $validate = array(
		'subject' => array(
			'rule' => array('between', 5, 50),
			'required' => true,
			'message' => 'Doit comporter entre 5 et 150 caracteres.'
		),
		'textarea' => array(
			'rule' => array('minLength', '5'),
			'message' => 'Au moins 5 caracteres.'
		)
	);

}
?>