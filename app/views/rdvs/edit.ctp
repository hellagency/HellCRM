<?php
e($this->Form->create('Rdv'));
e($this->Form->input('id', array('type' => 'hidden')));
e($this->Form->input('Rdvcat', array('label' => 'Soins effectu&eacute;s')));
e($this->Form->input('date', array('dateFormat' => 'DMY', 'label' => 'Date du rendez-vous')));
e($this->Form->input('textarea', array('label' => 'Commentaire')));
e($ajax->submit('Editer', array('url'=> array('action'=>'edit'), 'update' => 'RdvEditForm')));
?>
</form>