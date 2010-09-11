<?php
e($this->Form->create('Card'));
e($this->Form->input('id', array('type' => 'hidden')));
e($this->Form->input('started', array('dateFormat' => 'DMY', 'label' => 'Date de d&eacute;but')));
e($this->Form->input('ending', array('dateFormat' => 'DMY', 'label' => 'Date de fin')));
e($this->Form->input('textarea', array('label' => 'Commentaire')));
e($ajax->submit('Editer', array('url'=> array('action'=>'create'), 'update' => 'CardEditForm')));
?>
</form>