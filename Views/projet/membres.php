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
      <li class="active"><a href="<?php echo site_url("projet/membres/".$projet->idprojet); ?>"><i class="icon-user icon-white"></i> Membres</a></li>
      <hr/>
      <?php if(!empty($isadmin)): ?>
        <li><a href="<?php echo site_url("projet/gestion/".$projet->idprojet); ?>"><i class="icon-wrench"></i> Gestion du projet</a></li>
      <?php endif; ?>
    </ul>
  </div>
  <div role="main" id="main">
     <div id="recherche">
      <form class="form-search" method="post" action="<?php echo site_url('recherche/generique"') ?>">
        <div class="input-append">
        <input type="text" class="span2 search-query" name="requete">
        <button type="submit" class="btn">Recherche</button><a href="<?php echo site_url("recherche/avance"); ?>">Recherche Avancée</a>
        </div>
      </form>
    </div>
    <div class="page-header">
      <h1><?php echo $projet->nom; ?><small> Membres</small></h1>
    </div>
    <?php if($isadmin): ?>
    <div class="well">
    <form id="ajouter-membre" action="<?php echo site_url("projet/ajouter_membre/".$projet->idprojet); ?>" method="post" >
    <div class="ui-widget">
      <table>
        <tr>
          <td>
            <label for="utilisateurs">Membres: </label>
            <input id="auto-utilisateurs" name="utilisateurs" class="input-xxlarge" />
          </td>
          <td>
            <label for="groupe">Groupe: </label>
            <select id="groupe" name="groupe">
              <?php
                foreach($groupes as $g) {
                  echo "<option value='".$g->idgroupe."''>".$g->type."</option>";
                }
              ?>
            </select> 
            </td>
          <td>
             <input type="submit" value="Envoyer" class="btn"/>
          </td>
        </tr>
      </table>
    </div>
  </form>
  </div>    
  <?php endif;?>
      <?php
        if(empty($membres)) {
          echo "Aucun membre";
        } else {
          echo "<table class='table'>";
          foreach($membres as $k => $groupe){
            echo '<tr><td><h3>'.$groupes[$k]->type.'</h3><ul class="membres">';
            foreach($groupe as $m) {
              echo '<li>';
              if($isadmin) {
                echo '<button data-id-utilisateur="'.$m->idutilisateur.'" data-id-projet="'.$m->idprojet.'" type="button" class="close" aria-hidden="true">&times;</button>';
              }
              echo '<a data-id="'.$m->idutilisateur.'">
                      <div class="image">';
              if(empty($m->image)){
                echo '<img  class="img-profil" src="'.site_url("img/profil_vide.png").'" />';
              } else {
                echo '<img  class="img-profil" src="'.site_url("uploads/profil/utilisateur/".$m->image).'" />';
              }
              echo '</div>
                  <div class="donnees"><strong>'.$m->nom.' '.$m->prenom.'</strong><br/> 
                    <i class="icon-envelope"></i> Contacter l\'utilisateur</div>
              </a></li>';
            }
            echo "</ul></td></tr>";
          }
          echo "</table>";
        }
      ?>
      <button class="btn" id="msgtous">Contacter tous les membres</button>
  </div>
  <div class="modal hide fade" id="modalmsg">
    <form id="form-message" action="<?php echo site_url("messagerie/contacter_util"); ?>" method="post">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Contacter membre <small>d</small></h3>
    </div>
    <div class="modal-body">
      <input type="hidden" name="idDest" id="idDest" />
      <label for="objet">Sujet :</label>
      <input type="text" name="objet" />
      <label for="message">Message :</label>
      <textarea name="message"></textarea>
    
    </div>
    <div class="modal-footer">
    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn">Fermer</button>
    <input type="submit" class="btn btn-primary" value="Envoyer" />
    </div>
    </form>
  </div>

  <div class="modal hide fade" id="modalmsgtous">
    <form id="form-message-touts" action="<?php echo site_url("messagerie/contacter_membre_projet"); ?>" method="post">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Contacter membres du projet</h3>
    </div>
    <div class="modal-body">
      <input type="hidden" name="idProjet" id="idProjet" value="<?php echo $projet->idprojet ?>"/>
      <label for="objet">Sujet :</label>
      <input type="text" name="objet" />
      <label for="message">Message :</label>
      <textarea name="message"></textarea>
    
    </div>
    <div class="modal-footer">
    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn">Fermer</button>
    <input type="submit" class="btn btn-primary" value="Envoyer" />
    </div>
    </form>
  </div>