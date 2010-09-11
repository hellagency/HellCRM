<table>
	<tr>
		<th>#</th>
		<th>Client</th>
		<th>D&eacute;but</th>
		<th>Fin</th>
		<th>Etat</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($cards as $card): ?>
		<tr>
			<td><?php e($card['Card']['id']); ?></td>
			<td><?php e($this->Html->link(
					$card['Client']['nom_entier'],
					array('controller'=>'clients', 'action'=>'view', $card['Client']['id'])
				)); ?></td>
			<td><?php e($time->format('d/m/Y', $card['Card']['started'])); ?></td>
			<td><?php e($time->format('d/m/Y', $card['Card']['ending'])); ?></td>
			<td><?php if (($time->fromString($card['Card']['ending'])+86399) > time()) e('<font color="green">Valide</font>'); else e('<font color="red">Expir&eacute;</font>'); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_editer.png'),
					array('action'=>'edit', $card['Card']['id'].'?height=500'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
				<?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('action'=>'delete', $card['Card']['id'].'?height=50'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?></td>
		</tr>
	<?php endforeach; ?>
</table>
<div style="text-align:center;">
	<?php e($paginator->numbers()); ?>
</div>

<?php e($paginator->counter(array(
	'format' => '<p>Affichage de %current% enregistrements sur un total de %count%</p>'
))); ?>