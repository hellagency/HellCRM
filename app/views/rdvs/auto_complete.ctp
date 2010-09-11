<ul>
     <?php foreach($users as $user): ?>
        <?php 
			$result = $text->highlight($user['Client']['prenom']." ".$user['Client']['nom'], $input, array ('format' => '<strong>\1</strong>'));
			e($this->Html->link(
				'<li>'.$result.'</li>',
				array('action'=>'add', $user['Client']['id']),
				array('escape' => false, 'class' => 'thickbox')
			));
			?>
     <?php endforeach; ?>
</ul>