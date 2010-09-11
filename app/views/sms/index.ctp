<table>
	Il vous reste <?php echo $credit ?> sms &agrave; envoyer.
	<tr>
		<th>#</th>
		<th>Client</th>
		<th>Message</th>
		<th>Envoy&eacute; le</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($sms as $message): ?>
		<tr>
			<td><?php echo $message['Sms']['id']; ?></td>
			<td><?php if ($mail['Sms']['client_id']==0) e('tous'); else e($this->Html->link($mail['Client']['nom_entier'],array('controller'=>'clients', 'action'=>'view', $message['Client']['id']))); ?></td>
			<td><?php echo $message['Sms']['textarea']; ?></td>
			<td><td><?php e($time->format('d/m/Y H:i', $message['Sms']['created'])); ?></td></td>
			<td><?php echo $this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('action'=>'delete', $message['Sms']['id']),
					array('escape' => false)
				); ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<div style="text-align:center;">
	<?php echo $paginator->numbers(); ?>
</div>