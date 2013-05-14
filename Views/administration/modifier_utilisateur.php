  <div role="main" id="main">
    <div class="page-header">
      <h1>Administration</h1>
    </div>
    <div class="well">
      <h2>
        Modifier utilisateur
      </h2>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <form action="<?php echo current_url(); ?>" method="post">
      <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="nom">Nom</label></th>
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
        <tr>
          <th><label for="superadmin">Super Administrateur</label></th>
          <td><input type="checkbox" name="superadmin" <?php if($utilisateur->superadmin) { echo "checked"; } ?> /> Donner les droits</td>
        </tr>
        <tr>
          <th><label for="adresse">Statut</label></th>
          <td>
			     <input type="radio" name="statut" value="actif" <?php if($utilisateur->statut == "actif") { echo "checked"; } ?> /> Actif
			     <input type="radio" name="statut" value="clos" <?php if($utilisateur->statut == "clos") { echo "checked"; } ?> /> Clos
			     <input type="radio" name="statut" value="bloque" <?php if($utilisateur->statut == "bloque") { echo "checked"; } ?> /> Bloqué
		      </td>
        </tr>
        <tr>
          <th><label for="invite">Mode invité</label></th>
          <td><input type="checkbox" name="invite" <?php if($utilisateur->invite) { echo "checked"; } ?> /> Activer</td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Envoyer</button>
    </form>
    </div>
  </div>