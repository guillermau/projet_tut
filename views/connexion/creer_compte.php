  <div role="main" id="main">
    <div class="page-header">
      <h1>Créer Compte</h1>
    </div>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <form action="<?php echo current_url(); ?>" method="post" class="form-horizontal">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" value="<?php echo set_value('email'); ?>" />

        <label for="mdp">Mot de passe</label>
        <input type="password" name="mdp" id="mdp" />

        <label for="mdpconf">Confirmation de mot de passe</label>
        <input type="password" name="mdpconf" id="mdpconf" />

        <label for="nom">NOM</label>
        <input type="text" name="nom" id="nom" value="<?php echo set_value('nom'); ?>" />

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="<?php echo set_value('prenom'); ?>" />

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="<?php echo set_value('adresse'); ?>" />
        
        <label for="adresse">Justification de l'inscription</label>
        <textarea name="justification" id="justification" rows="5" cols="20"></textarea>
        <br/>
        <input type="submit" class="btn" value="Envoyer" style="margin-top:20px;" />
      </form>
  </div>