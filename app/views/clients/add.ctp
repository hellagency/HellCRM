<?php
echo $this->Form->create('Client');
echo $this->Form->input('nom');
echo $this->Form->input('prenom');
echo $this->Form->input('adresse');
echo $this->Form->input('telephone');
echo $this->Form->input('email');
e($ajax->submit('Ajouter', array('url'=> array('controller'=>'clients', 'action'=>'add'), 'update' => 'ClientAddForm')));
?>
</form>