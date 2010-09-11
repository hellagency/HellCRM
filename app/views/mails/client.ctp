<?php
e($this->Form->create('Mail'));
e($this->Form->input('client_id', array('type' => 'hidden', 'default'=>$id)));
e($this->Form->input('subject', array( 'label' => 'Sujet' ) ));
e($this->Form->input('textarea', array('rows' => '5', 'cols' => '5', 'label' => 'Message')));
e($ajax->submit('Envoyer', array('url'=> array('controller'=>'mails', 'action'=>'client'), 'update' => 'MailClientForm')));
?>
</form>