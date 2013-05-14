<div role="navigation" id="menu-lateral">

  <h2>
    <?php 
      if(empty($projet->image)){
        echo '<img width="50" height="50" src="'.site_url("img/projet_vide.png").'" />';
      } else {
        echo '<img width="50" height="50" src="'.site_url("uploads/profil/projet/".$projet->image).'" />';
      }
      echo $projet->nom;
    ?>
  </h2>

  <hr style="margin-top:3px"/>
  <ul class="nav nav-pills">
    <li><a href="<?php echo site_url("projet/accueil/".$projet->idprojet); ?>"><i class="icon-home"></i> Accueil</a></li>
    <li><a href="<?php echo site_url("projet/documents/".$projet->idprojet); ?>"><i class="icon-file"></i> Documents</a></li>
    <li><a href="<?php echo site_url("projet/membres/".$projet->idprojet); ?>"><i class="icon-user"></i> Membres</a></li>
    <hr/>
    <li><a href="<?php echo site_url("projet/gestion/".$projet->idprojet); ?>"><i class="icon-wrench icon-black"></i> Gestion du projet</a></li>
  </ul>
</div>

<!-- Affichage données -->
<div role="main" id="main">
  <div class="page-header">
    <h1><?php echo $document->nom_original;?><small> Gestion de document</small></h1>
  </div>
  <div class="well" id="doc-donnee">
    <button class="btn btn-info" id="modifd">Modifier Document</button>
    <button class="btn" id="deplaced">Deplacer Document</button>
    <p><strong>Description</strong></p>
    <p id="doc-desc"><?php echo $document->description;?></p>
    <hr/>
    <p><strong>Tags</strong></p>
    <p id="doc-tags"><?php
      if(!empty($tags)) {
        $liste_tags = ''; 
        foreach($tags as $t) {
          $liste_tags .= ', '.$t->tag;
        }
        echo substr($liste_tags, 2);
      }
    ?></p>
  </div>

  <!-- Fenetre modification données -->
  <div class="well" id="doc-modif">
    <form action="<?php echo current_url(); ?>" method="post">
      <button class="btn btn-success" type="submit">Envoyer Modifications</button>
      <a id="doc-modif-cancel" class="btn btn" type="submit">Annuler</a>

      <label for="description">Description</label>
      <textarea id="description" name="description"><?php echo $document->description; ?></textarea>
      <hr/>
      <label for="tags">Tags</label>
      <div class="ui-widget"><input type="text" name="tags" id="tags" value="<?php
        if(!empty($tags)) {
          $liste_tags = ''; 
          foreach($tags as $t) {
            $liste_tags .= ', '.$t->tag;
          }
          echo substr($liste_tags, 2);
        }
      ?>" /></div><br/><span class="help-block">Séparez les tags par des virgules.</span>
    </form>
  </div>

  <!-- Affichage des droits -->
  <div class="well">
    <h2>Groupes</h2>

    <ul id="doc-groupes">
    <?php
      foreach($groupes as $g) {
        $v = $e = $l = "";
        if(!empty($g->visualisation))   $v = 'checked="checked"';
        if(!empty($g->ecriture)) $e = 'checked="checked"';
        if(!empty($g->lecture))  $l = 'checked="checked"';
        echo '<li data-id-groupe="'.$g->idgroupe.'">'.$g->type.' <div class="editbtn">';
        echo '<form class="doc-mod-droits" method="post" action="'.site_url("document/modifier-groupe/".$document->iddocument).'">';
        echo '<input type="checkbox" '.$v.' name="droits[]" value="visualisation"   /> Visualisation ';
        echo '<input type="checkbox" '.$l.' name="droits[]" value="lecture"  /> Lecture ';
        echo '<input type="checkbox" '.$e.' name="droits[]" value="ecriture" /> Ecriture ';
        echo '<input type="hidden" name="idgroupe" value="'.$g->idgroupe.'" />';
        echo '<button class="btn">Enregistrer</button></form></div></li>';
      }
    ?>
    </ul>
  </div>

  <!-- Fonction de supression -->
  <a id="doc-suppr" href="<?php echo site_url('document/supprimer/'.$document->iddocument) ?>" class="btn btn-danger">Supprimer Document</a>
</div>

<!-- Fenettre de déplacement -->
<div id="modal-move" class="modal hide fade">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h3>Deplacer le fichier</h3>
  </div>
  <form id="form-move" method="post" action="<?php echo site_url("document/deplacer/".$document->iddocument); ?>">
    <div class="modal-body">
        <label for="idnvdossier">Répertoire cible : </label>
        <select id="idnvdossier" name="idnvdossier">
          <option value="0">Racine</option>
          <?php
            foreach($repertoires as $r)
              echo '<option value="'.$r->idrepertoire.'">'.$r->nom.'</option>';
          ?>
        </select>
    </div>
    <div class="modal-footer">
      <a href="#" class="btn" data-dismiss="modal">Fermer</a>
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>

</div>