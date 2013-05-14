<div role="main" id="main">
  <div id="recherche">
    <form class="form-search" method="post" action="<?php echo site_url('recherche/generique') ?>">
      <div class="input-append">
      <input type="text" class="span2 search-query" name="requete">
      <button type="submit" class="btn">Recherche</button><a href="<?php echo site_url("recherche/avance"); ?>">Recherche Avancée</a>
      </div>
    </form>
  </div>

  <div class="page-header">
    <h1>Recherche<small> Résultats</small></h1>
  </div>

  <?php
    if(!empty($users)) {
      echo "<h3>Utilisateurs</h3> <table class='table table-striped'>";
      foreach($users as $k => $u){
        if(empty($u->image)){
          echo '<img width="50" height="50" src="'.site_url("img/profil_vide.png").'" />';
        } else {
          echo '<img width="50" height="50" src="'.site_url("uploads/profil/utilisateur/".$u->image).'" />';
        }
          echo' <a href="'.site_url('utilisateur/apercu/'.$u->idutilisateur).'">'.$u->nom.' '.$u->prenom.'</a>';
      }
      echo "</table>";
    }

    if(!empty($projets)) {
      echo "<h3>Projets</h3> <table class='table table-striped'>";
      foreach($projets as $p){
          echo '<tr><td>';
          if(empty($p->image)){
            echo '<img width="50" height="50" src="'.site_url("img/projet_vide.png").'" />';
          } else {
            echo '<img width="50" height="50" src="'.site_url("uploads/profil/projet/".$p->image).'" />';
          }
            echo' <a href="'.site_url('projet/apercu/'.$p->idprojet).'">'.$p->nom.'</a>';
         
          echo '</td></tr>';
        }  
       echo "</table>";
    }

      if(!empty($documents)) {
        echo "<h3>Documents</h3> <table class='table table-striped'>";
        foreach($documents as $d){
          echo '<tr class="docboite"><td class="'.$type[$d->type]->type.'"">';
          echo' <a href="'.site_url('document/apercu/'.$d->iddocument).'">'.$d->nom_original.'</a>';
          echo '</td></tr>';
        }  
        echo "</table>";
      }
  ?>

</div>