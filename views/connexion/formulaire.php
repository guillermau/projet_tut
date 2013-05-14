<div role="main">

    <div class="well" style="width:230px;margin:auto">
      	<?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      	<?php if(!empty($erreur)): ?>
      		    <div class="alert alert-error">
    				<?php echo $erreur; ?>
  				</div>
      	<?php endif; ?>
        <?php if($this->session->flashdata('erreur')): ?>
              <div class="alert alert-error">
            <?php echo $this->session->flashdata('erreur'); ?>
          </div>
        <?php endif; ?>
		<form method="post" action="<?php echo site_url("session/connexion") ?>">
			<legend>Connexion</legend>
			<label for="email">Email: </label>
				<input type="email" placeholder="Votre email" name="email" id="email" />
			<label for="mdp">Mot de passe: </label>
				<input type="password" name="mdp" id="mdp" placeholder="Votre mot de passe" />
			<button type="submit" class="btn btn-success">Envoyer</button>
		</form>
    </div>
    <div style="text-align:right;width:260px;margin:10px auto;">
      <a href="<?php echo site_url("session/recuperation-mdp"); ?>" class="btn">Récupérer mot de passe</a>
      <a href="<?php echo site_url("session/inscription"); ?>" style="margin-top:10px" class="btn btn-info">Créer un compte</a>
    </div>
  </div>