<?php
 define('NBR_LGNS_SCND_LN', 9);
?>
<div style="text-align:center;"><a href="#infos">Informations</a> | <a href="#rdv">Rendez-vous</a> | <a href="#cards">Cartes d'abonnement</a> | <a href="#sms">SMS</a> | <a href="#mails">Mails</a></div>
<a name="infos"></a>
<fieldset>
<legend>Informations sur le client</legend>
<h3><?php e($infos['Client']['nom_entier']); ?> <?php e($this->Html->link($this->Html->image('icone_editer_mini.png'),array('action'=>'edit', $infos['Client']['id'].'?height=550'),array('escape' => false, 'class' => 'thickbox'))); ?> <?php e($this->Html->link($this->Html->image('icone_effacer_mini.png'),array('action'=>'delete', $infos['Client']['id']),array('escape' => false, 'class' => 'thickbox'))); ?></h3>
<p><?php if(!empty($infos['Client']['adresse'])) e($infos['Client']['adresse']); else e('<em>Adresse inconnue</em>'); ?></p>
<p><br/><?php if(!empty($infos['Client']['telephone'])) e($this->Html->link($infos['Client']['telephone'],array('controller'=>'sms', 'action'=>'client', $infos['Client']['id'].'?height=300'),array('escape' => false, 'class' => 'thickbox'))); else e('<em>N&deg; de tel. inconnu</em>'); ?>
 | <?php if(!empty($infos['Client']['email'])) e($this->Html->link($infos['Client']['email'],array('controller'=>'mails', 'action'=>'client', $infos['Client']['id'].'?height=400'),array('escape' => false, 'class' => 'thickbox'))); else e('<em>Adr. e-mail inconnue</em>'); ?></p>
</fieldset>

<a name="rdv"></a>
<fieldset>
<legend>Rendez-vous</legend>
	<?php 
	$nouveau_rdv_lien = '<div class="alignright">'.$this->Html->link('+ Nouveau', array('controller'=>'rdvs', 'action'=>'add', $infos['Client']['id'].'?height=500'), array('class' => 'thickbox')).'</div>';
	if(isset($infos['Rdv'][0])) {
		if(isset($infos['Rdv'][NBR_LGNS_SCND_LN])) 
			e($nouveau_rdv_lien); 
	?>
	<table>
		<tr>
			<th>#</th>
			<th>Soins</th>
			<th>Commentaire</th>
			<th>Effectu&eacute; le</th>
			<th>&nbsp;</th>
		</tr>
	<?php foreach($rdvs as $rdv): ?>
		<tr>
			<td><?php e($rdv['Rdv']['id']); ?></td>
			<td><?php if (!isset($rdv['Rdvcat'][0])) e('Aucun'); else foreach($rdv['Rdvcat'] as $Rdvcat) e($Rdvcat['name'].', '); ?></td>
			<td><?php e($rdv['Rdv']['textarea']); ?></td>
			<td><?php e($time->format('d/m/Y', $rdv['Rdv']['date'])); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_editer.png'),
					array('controller'=>'rdvs', 'action'=>'edit', $rdv['Rdv']['id'].'?height=500'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
				<?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('controller'=>'rdvs', 'action'=>'delete', $rdv['Rdv']['id'].'?height=50'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php } else e('<p>Pas de RDV li&eacute;s &agrave; ce client</p>');
	e($nouveau_rdv_lien); ?>
</fieldset>

<a name="cards"></a>
<fieldset>
<legend>Cartes d'abonnement</legend>
	<?php 
	$nouvelle_card_lien = '<div class="alignright">'.$this->Html->link('+ Nouvelle', array('controller'=>'cards', 'action'=>'create', $infos['Client']['id'].'?height=300'),array('class' => 'thickbox')).'</div>';
	if(isset($infos['Card'][0])) {  
		if(isset($infos['Card'][NBR_LGNS_SCND_LN])) e($nouvelle_card_lien); ?>
	<table>
		<tr>
			<th>#</th>
			<th>D&eacute;but</th>
			<th>Fin</th>
			<th>Etat</th>
			<th>&nbsp;</th>
		</tr>
	<?php foreach($infos['Card'] as $card): ?>
		<tr>
			<td><?php e($card['id']); ?></td>
			<td><?php e($time->format('d/m/Y', $card['started'])); ?></td>
			<td><?php e($time->format('d/m/Y', $card['ending'])); ?></td>
			<td><?php if ($time->fromString($card['ending'])+86399 > time()) e('<font color="green">Valable</font>'); else e('<font color="red">Expir&eacute;</font>'); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_editer.png'),
					array('controller'=>'cards', 'action'=>'edit', $card['id'].'?height=500'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?>
				<?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('controller'=>'cards', 'action'=>'delete', $card['id'].'?height=50'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php } else e('<p>Pas de Cartes d\'abonnement li&eacute;es &agrave; ce client</p>');
	e($nouvelle_card_lien); ?>
</fieldset>

<a name="sms"></a>
<fieldset>
<legend>SMS envoy&eacute;s</legend>
	<?php 
	$nouveau_sms_lien = !empty($infos['Client']['telephone']) ? '<div class="alignright">'.$this->Html->link('+ Nouveau', array('controller'=>'sms', 'action'=>'client', $infos['Client']['id'].'?height=300'),array('class' => 'thickbox')).'</div>': "<div class='alignright'>N&deg; de tel. requis</div>";
	if(isset($infos['Sms'][0])) { 
		if(isset($infos['Sms'][NBR_LGNS_SCND_LN])) e($nouveau_sms_lien); ?>
	<table>
		<tr>
			<th>#</th>
			<th>Message</th>
			<th>Envoy&eacute; le</th>
			<th>&nbsp;</th>
		</tr>
	<?php foreach($infos['Sms'] as $sms): ?>
		<tr>
			<td><?php e($sms['id']); ?></td>
			<td><?php e($sms['textarea']); ?></td>
			<td><?php e($time->format('d/m/Y H:i', $sms['created'])); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('controller'=>'sms', 'action'=>'delete', $sms['id'].'?height=50'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php } else e('<p>Pas de SMS envoy&eacute;s &agrave; ce client</p>');
	e($nouveau_sms_lien); ?>
</fieldset>

<a name="mails"></a>
<fieldset>
<legend>E-Mails envoy&eacute;s</legend>
	<?php 
	$nouveau_mail_lien = !empty($infos['Client']['email']) ? '<div class="alignright">'.$this->Html->link('+ Nouveau', array('controller'=>'mails', 'action'=>'client', $infos['Client']['id'].'?height=300'),array('class' => 'thickbox')).'</div>' : "<div class='alignright'>Adr. e-mail requise</div>";
	if(isset($infos['Mail'][0])) {  
		if(isset($infos['Mail'][NBR_LGNS_SCND_LN])) e($nouveau_mail_lien); ?>
	<table>
		<tr>
			<th>#</th>
			<th>Sujet</th>
			<th>Message</th>
			<th>Envoy&eacute; le</th>
			<th>&nbsp;</th>
		</tr>
	<?php foreach($infos['Mail'] as $mail): ?>
		<tr>
			<td><?php e($mail['id']); ?></td>
			<td><?php e($mail['subject']); ?></td>
			<td><?php e($mail['textarea']); ?></td>
			<td><?php e($time->format('d/m/Y H:i', $mail['created'])); ?></td>
			<td><?php e($this->Html->link(
					$this->Html->image('icone_effacer.png'),
					array('controller'=>'mails', 'action'=>'delete', $mail['id'].'?height=50'),
					array('escape' => false, 'class' => 'thickbox')
				)); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php } else e('<p>Pas de E-Mails envoy&eacute;s &agrave; ce client</p>');
	e($nouveau_mail_lien); ?>
</fieldset>