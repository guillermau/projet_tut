  <div role="main" id="main">
    <div class="page-header">
      <h1>Mon Compte</h1>
    </div>
    <a href="<?php echo site_url('mon-compte'); ?>">Retour</a>
    <div class="well">
      <h2>
        Modifier image de profil
      </h2>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <?php if(!empty($echec)): ?>
              <div class="alert alert-error">
            <?php echo $echec; ?>
          </div>
        <?php endif; ?>
      <?php 
        if(!empty($utilisateur->image)){
          echo '<img  class="img-profil" src="'.site_url("uploads/profil/utilisateur/".$utilisateur->image).'" />';
          echo '<a href="'.site_url("membre/supprimer_image").'">Supprimer image</a>';
        }
      ?> 
      <hr/>
      <form action="<?php echo current_url(); ?>" method="post" enctype="multipart/form-data">
      <label for="image">Choisissez l'image: </label>
      <input type="file" name="image" id="image" />
      <span class="help-block">(de préférence une image carrée)</span>
    <button type="submit" class="btn">Envoyer</button>
    </form>
    </div>
  </div>