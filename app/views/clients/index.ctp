<h1>
	  <?php e($this->Html->link('Envoyer un SMS de masse',array('controller' => 'sms', 'action' => 'allclients'),array('class' => 'thickbox'))); ?>
	| <?php e($this->Html->link('Envoyer un mail de masse',array('controller' => 'mails', 'action' => 'allclients'),array('class' => 'thickbox'))); ?>
	| <?php e($this->Html->link('Ajouter un client',array('action'=>'add', '?height=550'),array('class' => 'thickbox'))); ?>
</h1>
	
<?php 
e($form->create('Client', array('url' => '/'))); ?>
	<?php e($ajax->autoComplete('input', '/clients/autoComplete', array('minChars' => 2, 'default' => 'Rechercher un client', 'indicator' => 'spinner'))); ?>
</form>
<table>
	<tr>
		<th><?php e($paginator->sort('#', 'id')); ?></th>
		<th><?php e($paginator->sort('Nom', 'nom')); ?></th>
		<th><?php e($paginator->sort('Prenom', 'prenom')); ?></th>
		<th><?php e($paginator->sort('Adresse', 'adresse')); ?></th>
		<th><?php e($paginator->sort('Telephone', 'telephone')); ?></th>
		<th><?php e($paginator->sort('E-mail', 'email')); ?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($clients as $client): ?>
		<tr>
			<td><?php e($client['Client']['id']); ?></td>
			<td><?php e($client['Client']['nom']); ?></td>
			<td><?php e($client['Client']['prenom']); ?></td>
			<td><?php e($client['Client']['adresse']); ?></td>
			<td><?php e($this->Html->link(
					$client['Client']['telephone'],
					array('controller'=>'sms', 'action'=>'client', $client['Client']['id'].'?height=300'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
			</td>
			<td><?php e($this->Html->link(
					$client['Client']['email'],
					array('controller'=>'mails', 'action'=>'client', $client['Client']['id'].'?height=400'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
			</td>
			<td><?php e(
						$this->Html->link(
							$this->Html->image('icone_voir.png'),
							array('action'=>'view', $client['Client']['id']),
							array('escape' => false)
						)
						.'&nbsp;'.$this->Html->link(
							$this->Html->image('icone_editer.png'),
							array('action'=>'edit', $client['Client']['id'].'?height=500'),
							array('escape' => false, 'class' => 'thickbox')
						)
						.'&nbsp;'.$this->Html->link(
							$this->Html->image('icone_effacer.png'),
							array('action'=>'delete', $client['Client']['id'].'?height=50'),
							array('escape' => false, 'class' => 'thickbox')
						)
					); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
<div style="text-align:center;">
	<?php e($paginator->numbers()); ?>
</div>

<?php e($paginator->counter(array(
	'format' => '<p>Affichage de %current% enregistrements sur un total de %count%</p>'
))); ?>
