<div role="main">

    <div class="well" style="width:230px;margin:auto">
      	<?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      	<?php if(!empty($erreur)): ?>
      		    <div class="alert alert-error">
    				<?php echo $erreur; ?>
  				</div>
      	<?php endif; ?>
		<form method="post" action="<?php echo current_url() ?>">
			<legend>Récupérer mot de passe</legend>
			<label for="email">Nouveau mot de passe: </label>
				<input type="password" placeholder="Nouveau mot de passe" name="mdp" id="mdp" /><br />
				<input type="password" placeholder="Confirmation" name="mdpconf" id="mdpconf" />
			<button type="submit" class="btn btn-success">Envoyer</button>
		</form>
    </div>
    <div style="text-align:right;width:260px;margin:10px auto;">
      <a href="<?php echo site_url("session/connexion"); ?>" style="margin-top:10px" class="btn">Retour</a>
    </div>
  </div>