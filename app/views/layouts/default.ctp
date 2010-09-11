<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php e($this->Html->charset()); ?>
	<title>
		<?php __('HellCRM :'); ?>
		<?php e($title_for_layout); ?>
	</title>
	<?php
		e($this->Html->meta('icon'));

		e($this->Html->css('cake.generic'));
		
		e($this->Html->css('thickbox'));
		
		e($html->script('prototype'));
		
		e($html->script('scriptaculous.js')); 
		
		e($html->script('jquery.js'));
		
		e($html->script('jquery-ui.js')); 
		
		e($html->script('thickbox.js')); 
		
		e($scripts_for_layout);
	?>
	<script>
		jQuery.noConflict();
	</script>
</head>
<body>
	<div id="spinner" style="display: none;">
			<?php e($this->Html->link($html->image('spinner.gif'), '', array('escape' => false))); ?>
	</div>
	<div id="container">
		<div id="header">
			<h1><?php e($this->Html->link(__('HellCRM', true), array('controller' => 'clients', 'action' => 'index'))); ?> > <?php e($title_for_layout); ?>
				<div id="header_right">
					<?php e($this->Html->link(
								$html->image('sauver-icone-6403-32.png'),
								'../cron/backupDB.php?StartBackup=complete&nohtml=1',
								array('escape' => false, 'class' => 'backupDB', 'target' => '_blank', 'alt' => 'Sauvegarder', 'title' => 'en cliquant sur ce lien vous sauvegarderez la base de donn&eacute;e et vous pourrez la t&eacute;l&eacute;charger.' )
							)); ?>
				</div>
			</h1>
		</div>
		<div id="content">

			<?php e($this->Session->flash()); ?>

			<?php e($content_for_layout); ?>

		</div>
		<div id="footer">
			<?php
				if (!is_null($session->read('Auth.User')))
					e($this->Html->link(
						'D&eacute;connexion',
						array('controller' => 'users', 'action' => 'logout'),
						array('escape' => false)
					));
				else
					e($this->Html->link(
						'Connexion',
						array('controller' => 'users', 'action' => 'login'),
						array('escape' => false)
					));
			?>
		</div>
	</div>
	<?php e($this->element('sql_dump')); ?>
</body>
</html>