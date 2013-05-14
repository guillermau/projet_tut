  <div role="main" id="main">
    <div class="page-header">
      <h1>Mon Compte</h1>
    </div>
    <a href="<?php echo site_url('mon-compte'); ?>">Retour</a>
    <div class="well">
      <h2>
        Modifier données
      </h2>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <form action="<?php echo current_url(); ?>" method="post">
      <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="nom">NOM</label></th>
          <td><input type="text" name="nom" id="nom" value="<?php echo $utilisateur->nom; ?>"/></td>
        </tr>
        <tr>
          <th><label for="prenom">Prénom</label></th>
          <td><input type="text" name="prenom" id="prenom" value="<?php echo $utilisateur->prenom; ?>"/></td>
        </tr>
        <tr>
          <th><label for="email">E-mail</label></th>
          <td><input type="email" name="email" id="email" value="<?php echo $utilisateur->email; ?>"/></td>
        </tr>
        <tr>
          <th><label for="adresse">Adresse</label></th>
          <td><input type="text" name="adresse" id="adresse" value="<?php echo $utilisateur->adresse; ?>"/></td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Envoyer</button>
    </form>
    </div>
  </div>