<table>
	<tr>
		<th>#</th>
		<th>Nom</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($cats as $cat): ?>
		<tr>
			<td><?php echo $cat['Rdvcat']['id']; ?></td>
			<td><?php echo $cat['Rdvcat']['name']; ?></td>
			<td>
				<?php echo $this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('action'=>'delete', $cat['Rdvcat']['id']),
					array('escape' => false)
				);
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<div style="text-align:center;">
	<?php echo $paginator->numbers(); ?>
</div>

<?php e($paginator->counter(array(
	'format' => '<p>Affichage de %current% enregistrements sur un total de %count%</p>'
))); ?>

<?php
echo $this->Html->link(
	'Creer une nouvelle catégorie',
	array('action'=>'add')
);
?>