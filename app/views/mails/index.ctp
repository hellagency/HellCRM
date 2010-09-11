<table>
	<tr>
		<th>#</th>
		<th>Client</th>
		<th>Sujet</th>
		<th>Message</th>
		<th>Envoy&eacute; le</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($mails as $mail): ?>
		<tr>
			<td><?php e($mail['Mail']['id']); ?></td>
			<td><?php if ($mail['Mail']['client_id']==0) e('tous'); else e($this->Html->link($mail['Client']['nom_entier'],array('controller'=>'clients', 'action'=>'view', $mail['Client']['id']))); ?></td>
			<td><?php e($mail['Mail']['subject']); ?></td>
			<td><?php e($mail['Mail']['textarea']); ?></td>
			<td><?php e($time->format('d/m/Y H:i', $mail['Mail']['created'])); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('action'=>'delete', $mail['Mail']['id'].'?height=50'),
					array('escape' => false)
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