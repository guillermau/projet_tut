<?php echo $doc->nom_original; ?>@/@
  <?php
    if ($doc->idrepertoire == '0')
      $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->chemin_fichier);
    else
      $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->idrepertoire."/".$doc->chemin_fichier);
  ?>

  <table>
    <tr>
      <td>
        <p><strong>Propriétaire:</strong> <?php echo $proprietaire->nom." ".$proprietaire->prenom; ?></p>
        <p><strong>Projet:</strong> <?php echo $projet->nom; ?></p>
      </td>
      <td>
        <p><strong>Type:</strong> <?php echo $type->type ?></p>
        <p><strong>Création:</strong> <?php echo date("d/m/Y h:i:s",strtotime($doc->creation)); ?></p>
        <p><strong>Mise à jour:</strong> <?php echo date("d/m/Y h:i:s",strtotime($doc->maj)); ?></p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>
        <strong>Tags</strong><br/>
        <?php 
          $tagline = "";
          if(!empty($tags))
            foreach($tags as $t) {
              $tagline .= ", ".$t->tag;
            }
          echo substr($tagline, 2);
          ?>
        </p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p><strong>Description</strong><br/>
        <?php echo $doc->description; ?>
      </p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <a href="<?php echo site_url("document/telecharger/".$doc->iddocument); ?>" class="btn" id="doc-telecharger">Telecharger</a>
        <a href="<?php echo site_url("document/gestion/".$doc->iddocument); ?>" class="btn btn-info" id="doc-edit">Editer</a>
        <a href="<?php echo site_url("document/supprimer/".$doc->iddocument); ?>" class="btn btn-danger">Supprimer</a>
      </td>
    </tr>
  </table>