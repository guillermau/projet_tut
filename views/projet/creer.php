  <div role="main" id="main">
    <a href="<?php echo site_url("mes-projets"); ?>">Retour</a>
    <div class="page-header">
      <h1>Créer Projet</h1>
    </div>
      <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
      <form action="<?php echo current_url(); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?php echo set_value('nom'); ?>" class="span5" />

        <label for="description">Description</label>
        <textarea name="description" class="span5">
          <?php echo set_value('description'); ?>
        </textarea>
        <label for="tags">Tags</label>
        <input type="text" name="tags" id="tags" value="<?php echo set_value('tags'); ?>" class="span5" />
        <p><br/>
          <label for="image">Image du projet: </label>
          <input type="file" name="image" id="image" />
        </p>
        <input type="submit" class="btn" value="Envoyer" style="margin-top:20px;" />
      </form>
  </div>