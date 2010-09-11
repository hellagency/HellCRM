<?php
e($this->Form->create('Sms'));
e($this->Form->input('client_id', array('type' => 'hidden', 'default'=>$id)));
e($this->Form->input('textarea', array('rows' => '5', 'cols' => '5', 'label' => 'Message')));
e("Apr&egrave;s l'envoi de ce sms il vous restera ".$credit_after." sms &agrave; envoyer");
e($ajax->submit('Envoyer', array('url'=> array('controller'=>'sms', 'action'=>'client'), 'update' => 'SmsClientForm')));
?>
</form>