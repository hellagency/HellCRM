<?php
class Client extends AppModel {
	
	var $hasMany = array(
			'Mail' => array('conditions' => array('Mail.visible >' => 0)),
			'Sms' => array('conditions' => array('Sms.visible >' => 0)), 
			'Rdv' => array('conditions' => array('Rdv.visible >' => 0)),
			'Card' => array('conditions' => array('Card.visible >' => 0))
		);
	
	var $virtualFields = array(
		'nom_entier' => 'CONCAT(Client.prenom, " ", Client.nom)'
	);


	var $validate = array(
		'nom' => array(
			'rule' => array('between', 3, 75),
			'required' => true,
			'message' => 'Doit comporter entre 3 et 75 caracteres.'
		),
		'prenom' => array(
			'rule' => array('between', 3, 75),
			'required' => true,
			'message' => 'Doit comporter entre 3 et 75 caracteres.'
		),
		'adresse' => array(
			'rule' => array('between', 10, 150),
			'allowEmpty' =>  true,
			'message' => 'Ce n\'est pas une adresse.'
		),
		'telephone' => array(
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'allowEmpty' =>  true,
				'message' => 'Ce n\'est pas un numero de telephone.'
			),
			'between' =>  array(
				'rule' => array('between', 10, 14),
				'allowEmpty' =>  true,
				'message' => 'Doit comporter entre 10 et 14 caracteres.'
			)
		),
		'email' => array(
			'rule' => 'email',
			'allowEmpty' =>  true,
			'message' => 'Ce n\'est pas une adresse email.'
		)
	);

}
?>
