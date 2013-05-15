<div role="navigation" id="menu-lateral">
    <h2>Mes Projets</h2>
    <hr style="margin-top:3px"/>
    <ul class="nav nav-pills">
      <?php
        if(!empty($projets)) {
          foreach($projets as $p){
            echo '<li><a href="'.site_url('projet/accueil/'.$p->idprojet).'">'.$p->nom.'</a></li>';
          }  
        } else {
          echo 'Aucun projet.';
        }
        if($utilisateur->invite == 0) {
          echo "<hr/>";
          echo '<li><a href="'.site_url('projet/creer/').'">Creer un nouveau projet</a></li>';
        }
       ?>
    </ul>

  </div>

  <div role="main" id="main">
    <div id="recherche">
      <form class="form-search" method="post" action="<?php echo site_url('recherche/generique') ?>">
        <div class="input-append">
        <input type="text" class="span2 search-query" name="requete" />
        <button type="submit" class="btn">Recherche</button><a href="<?php echo site_url("recherche/avance"); ?>">Recherche Avancée</a>
        </div>
      </form>
    </div>
    <div class="page-header">
      <h1>Mes projets <small><?php echo $utilisateur->nom." ".$utilisateur->prenom; ?></small></h1>
    </div>
    <ul class="projets">
      <?php 
        if(empty($projets)){
          echo "Aucun projet.";
        } else {
          foreach($projets as $p){
            echo'
            <li><a href="'.site_url('projet/documents/'.$p->idprojet).'">
              <div class="image"><img src="';
            //Affichage de l'image (vide si aucune définie)
            if($p->image == NULL) {
              echo site_url('img/projet_vide.png');
            } else {
              echo site_url('uploads/profil/projet/'.$p->image);
            }
            echo '" width="50" height="50" /></div>
              <div class="donnees"><strong>'.$p->nom.'</strong><br/>Groupe: '.@$groupes[$p->idprojet]->type.'</div>
            </a></li>';
          } 
        }
      ?>
    </ul>

  </div>