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
      <li class="active"><a href="<?php echo site_url("projet/gestion/".$projet->idprojet); ?>"><i class="icon-wrench icon-white"></i> Gestion du projet</a></li>
    </ul>
  </div>

  <div role="main" id="main">
    <div class="page-header">
      <h1><?php echo $projet->nom;?><small> Gestion de projet</small></h1>
    </div>
    <div class="well" id="donneesprojet">
      <button class="btn btn-info" id="modifp">Modifier Projet</button>
      <p><strong>Nom du projet</strong></p>
      <p id="proj-nom"><?php echo $projet->nom;?></p>
      <hr/>
      <p><strong>Description</strong></p>
      <p id="proj-desc"><?php echo $projet->description; ?></p>
      <hr/>
      <p><strong>Date de création</strong></p> <p><?php echo $projet->creation ?></p>
    <hr/>
      <p><strong>Tags</strong></p>
      <p id="proj-tags"><?php
        if(!empty($projet->tags)) {
          $liste_tags = ''; 
          foreach($projet->tags as $t) {
            $liste_tags .= ', '.$t->tag;
          }
          echo substr($liste_tags, 2);
        }
      ?></p>
      </div>
    <div class="well" id="modifprojet">
      <form action="<?php echo current_url(); ?>" method="post">
        <button class="btn btn-success" type="submit">Envoyer Modifications</button>
        <a id="proj-modif-cancel" class="btn btn" type="submit">Annuler</a>
      <label for="nom">Nom du projet</label>
      <input type="text" name="nom" id="nom" value="<?php echo $projet->nom;?>" />
      <hr/>
      <label for="description">Description</label>
      <textarea id="description" name="description"><?php echo $projet->description; ?></textarea>
    <hr/>
      <label for="tags">Tags</label>
      <input type="text" name="tags" id="tags" value="<?php
        if(!empty($projet->tags)) {
          $liste_tags = ''; 
          foreach($projet->tags as $t) {
            $liste_tags .= ', '.$t->tag;
          }
          echo substr($liste_tags, 2);
        }
      ?>" /><br/><span class="help-block">Séparez les tags par des virgules.</span>
    </form>
    <a class="btn btn-info" href="<?php echo site_url("projet/modifier-image/".$projet->idprojet); ?>">Modifier image</a>
    </div>
    <div class="well">
      <button id="ajoutgrp" class="btn btn-primary">Ajouter groupe</button>
      <h2>Groupes</h2>

      <ul id="proj-groupes">
      <?php
        foreach($groupes as $g) {
          $u = $e = $l = "";
          if(!empty($g->upload))   $u = 'checked="checked"';
          if(!empty($g->ecriture)) $e = 'checked="checked"';
          if(!empty($g->lecture))  $l = 'checked="checked"';
          echo '<li data-id-groupe="'.$g->idgroupe.'">'.$g->type.' <div class="editbtn">';
          echo '<form class="mod-droits" method="post" action="'.site_url("projet/modifier-groupe/".$projet->idprojet).'">';
          echo '<input type="checkbox" '.$l.' name="droits[]" value="lecture"  /> Lecture ';
          echo '<input type="checkbox" '.$e.' name="droits[]" value="ecriture" /> Ecriture ';
          echo '<input type="checkbox" '.$u.' name="droits[]" value="upload"   /> Upload ';
          echo '<input type="hidden" name="idgroupe" value="'.$g->idgroupe.'" />';
          echo '<button class="btn">Enregistrer</button></form> <a data-id-groupe="'.$g->idgroupe.'" class="btn supprimer" href="'.site_url("projet/supprimer-groupe/".$projet->idprojet."/".$g->idgroupe).'"><img title="supprimer" src="'.site_url("img/delete.png").'" width="16" height="16" alt="Supprimer" /></a></div></li>';
        }
      ?>
      </ul>
    </div>
    <a id="supprimer" href="<?php echo site_url('projet/supprimer/'.$projet->idprojet) ?>" class="btn btn-danger">Supprimer Projet</a>
  </div>
  <div id="modal-groupe" class="modal hide fade">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Ajouter nouveau groupe</h3>
    </div>
    <form id="form-groupe" method="post" action="<?php echo site_url("projet/creer-groupe/".$projet->idprojet); ?>">
      <div class="modal-body">
          <label for="description">Nom du groupe: </label>
          <input class="span5" name="type" id="type" /><br/>
          <label for="droits">Droits: </label>
          <input type="checkbox" name="droits[]" id="dl" value="lecture"  /> Lecture<br/>
          <input type="checkbox" name="droits[]" id="de" value="ecriture" /> Ecriture<br/>
          <input type="checkbox" name="droits[]" id="du" value="upload"   /> Upload<br/><br/>
          <input type="hidden" name="idprojet" id="idprojet" value="<?php echo $projet->idprojet; ?>" />
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Fermer</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>

    </div>