<?php
e($this->Form->create('Sms'));
e($this->Form->input('textarea', array('rows' => '5', 'cols' => '5', 'label' => 'Message')));
e("Vous allez envoyer ".$numberofsmstosend." sms, apr&egrave;s l'envoi de ceux-ci il vous restera ".$credit_after." sms &agrave; envoyer");
e($ajax->submit('Envoyer', array('url'=> array('controller'=>'sms', 'action'=>'allclients'), 'update' => 'SmsAllclientsForm', 'indicator' => 'spinner')));
?>