<?php
class User extends AppModel {
	var $validate = array(
		'username' => array(
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'required' => true,
				'message' => 'Uniquement des chiffres et des lettres.'
			),
			'between' =>  array(
				'rule' => array('between', 5, 50),
				'message' => 'Doit comporter entre 5 et 50 caractères.'
			)
		),
		'password' => array(
			'between' =>  array(
				'rule' => array('between', 5, 50),
				'required' => true,
				'message' => 'Doit comporter entre 5 et 50 caractères.'
			)
		)
	);

}
?>
