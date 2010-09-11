<ul>
     <?php foreach($users as $user): ?>
        <?php 
			$result = $text->highlight($user['Client']['prenom']." ".$user['Client']['nom'], $input, array ('format' => '<strong>\1</strong>'));
			echo $this->Html->link(
				'<li>'.$result.'</li>',
				array('action'=>'view', $user['Client']['id']),
				array('escape' => false)
			);
			?>
     <?php endforeach; ?>
</ul>