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
      <h1>Mes documents <small><?php echo $utilisateur->nom." ".$utilisateur->prenom; ?></small></h1>
    </div>
    <div class="btn-toolbar" style="text-align:right">
      <div class="btn-group">
        <a class="btn" id="liste" href="#"><i class="icon-list"></i></a>
        <a class="btn" id="boite" href="#"><i class="icon-th-large"></i></a>
      </div>
    </div>
    <?php 
      if(empty($documents)){
        echo "Aucun document.";
      } else {
        foreach($documents as $projdocs) {
          echo "<h2>Projet ".$projets[$projdocs[0]->idprojet]->nom."</h2>";
          echo '<ul class="docliste docs">';
          echo '<li class="entete">Nom du document<div class="meta"><span data-meta="date-maj">Mise à jour</span></div></li>';

          // Parcour liste documents
          foreach($projdocs as $doc){

            // Recherche chemin
            if ($doc->idrepertoire == '0')
              $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->chemin_fichier);
            else
              $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->idrepertoire."/".$doc->chemin_fichier);

              echo '<li class="'.$doc->type->type.'"><a class="downlink" target="_blank" href="'.$chemin.'"><img src="'.site_url("img/download.png").'" width="16" height="16" /></a>';
              echo '<a href="'.site_url("document/apercu/".$doc->iddocument).'">'.$doc->nom_original.'<div class="meta"><span data-meta="date-creation">'.date("d/m/Y",strtotime($doc->maj)).'</span></div></a></li>';
          }
          echo '</ul>';
        } 
      }
      
    ?>
  </div>

  <!-- Apercu -->
  <div id="modal-apercu" class="modal hide fade">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3>Nom du fichier</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-inverse" data-dismiss="modal">Fermer</a>
      </div>
  </div>