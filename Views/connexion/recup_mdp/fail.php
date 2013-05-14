<div role="main">
    <div class="well" style="width:230px;margin:auto">
		<h1>Erreur</h1>

		<p>Le compte associé à l'adresse email:<br/> <?php echo $email; ?> est introuvable. Celui-ci est peut-être bloqué ou clos.</p>
		<p>Veuillez contacter les administrateurs pour plus de renseignements.</p>
    </div>
    <div style="text-align:right;width:260px;margin:10px auto;">
      <a href="<?php echo site_url("session/connexion"); ?>" style="margin-top:10px" class="btn">Retour</a>
    </div>
</div>