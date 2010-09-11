<?php 
e($form->create('Rdv', array('url' => '/')));
	e($ajax->autoComplete('client_id', '/rdvs/autoComplete', array('default' => 'Entrez le nom d\'un client ici', 'minChars' => 2, 'indicator' => 'spinner'))); ?>
</form>
<table>
	<tr>
		<th><?php e($paginator->sort('#', 'id')); ?></th>
		<th><?php e($paginator->sort('Client', 'client_id')); ?></th>
		<th><?php e($paginator->sort('Type', 'rdvcat_id')); ?></th>
		<th><?php e($paginator->sort('Commentaire', 'textarea')); ?></th>
		<th><?php e($paginator->sort('Effectue le', 'date')); ?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($rdvs as $rdv): ?>
		<tr>
			<td><?php e($rdv['Rdv']['id']); ?></td>
			<td><?php e($this->Html->link(
					$rdv['Client']['nom_entier'],
					array('controller'=>'clients', 'action'=>'view', $rdv['Client']['id'])
				)); ?></td>
			<td><?php if (!isset($rdv['Rdvcat'][0])) e('Aucun'); else foreach($rdv['Rdvcat'] as $Rdvcat) e($Rdvcat['name'].', '); ?></td>
			<td><?php e($rdv['Rdv']['textarea']); ?></td>
			<td><?php e($time->format('d/m/Y', $rdv['Rdv']['date'])); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_editer.png'),
					array('action'=>'edit', $rdv['Rdv']['id'].'?height=500'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
				<?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('action'=>'delete', $rdv['Rdv']['id']),
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