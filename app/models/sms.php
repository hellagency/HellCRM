<?php
class Sms extends AppModel {
	var $validate = array(
		'textarea' => array(
			'rule' => array('minLength', '5'),
			'message' => 'Au moins 5 caractères.'
		)
	);
}
?>