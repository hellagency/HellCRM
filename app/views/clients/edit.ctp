<?php
e($this->Form->create('Client'));
e($this->Form->input('nom'));
e($this->Form->input('prenom'));
e($this->Form->input('adresse'));
e($this->Form->input('telephone'));
e($this->Form->input('email'));
e($this->Form->input('id', array('type' => 'hidden')));
e($ajax->submit('Editer', array('url'=> array('controller'=>'clients', 'action'=>'edit'), 'update' => 'ClientEditForm')));
?>
</form>