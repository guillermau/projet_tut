  <div role="main" id="main">
    <div class="page-header">
      <h1>Mon Compte</h1>
    </div>
    <a href="<?php echo site_url('mon-compte'); ?>">Retour</a>
    <div class="well">
      <h2>
        Modifier mot de passe
      </h2>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <?php if(!empty($echec)): ?>
              <div class="alert alert-error">
            <?php echo $echec; ?>
          </div>
        <?php endif; ?>
      <form action="<?php echo current_url(); ?>" method="post">
      <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="mdpanc">Ancien mot de passe</label></th>
          <td><input type="password" name="mdpanc" id="mdpanc" /></td>
        </tr>
        <tr>
          <th><label for="mdp">Nouveau mot de passe</label></th>
          <td><input type="password" name="mdp" id="mdp" /></td>
        </tr>
        <tr>
          <th><label for="mdpconf">Confirmation du nouveau mot de passe</label></th>
          <td><input type="password" name="mdpconf" id="mdpconf" /></td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Envoyer</button>
    </form>
    </div>
  </div>