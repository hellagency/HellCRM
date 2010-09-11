<?php
e($this->Form->create('Rdv'));
e($this->Form->input('client_id', array('type' => 'hidden', 'default'=>$client_id)));
e($this->Form->input('Rdvcat', array('label' => 'Soins effectu&eacute;s')));
e($this->Form->input('date', array('dateFormat' => 'DMY', 'label' => 'Date du rendez-vous')));
e($this->Form->input('textarea', array('label' => 'Commentaire')));
e($ajax->submit('Ajouter', array('url'=> array('action'=>'add'), 'update' => 'RdvAddForm')));
?>
</form>