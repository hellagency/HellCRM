<h2><?php
echo $this->Html->link(
	'Clients',
	array('controller'=>'clients', 'action'=>'index')
);
?></h2>
<h2><?php
echo $this->Html->link(
	'Mails',
	array('controller'=>'mails', 'action'=>'index')
);
?></h2>
<h2><?php
echo $this->Html->link(
	'SMS',
	array('controller'=>'sms', 'action'=>'index')
);
?></h2>