<div role="main" id="main">
    <div class="well">
      <h2>
        <?php 
        if(empty($utilisateur->image)){
          echo '<img  class="img-profil" src="'.site_url("img/profil_vide.png").'" />';
        } else {
          echo '<img  class="img-profil" src="'.site_url("uploads/profil/utilisateur/".$utilisateur->image).'" />';
        }
        echo $utilisateur->nom." ".$utilisateur->prenom;
        if($utilisateur->invite == 1) {
          echo '<span class="label label-info">Invité</span>';
        }

        if($utilisateur->superadmin == 1) {
          echo '<span class="label label-important"><img src="'.site_url("img/user_batman.png").'" width="16" height="16" />SuperAdmin!</span>';
        }
        ?>
      </h2>
      <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th>NOM, Prénom</th>
          <td><?php echo $utilisateur->nom." ".$utilisateur->prenom; ?></td>
        </tr>
        <tr>
          <th>E-mail</th>
          <td><?php echo $utilisateur->email; ?></td>
        </tr>
        <?php if($this->profil_model->verifier_superAdmin()): ?>
        <tr>
          <th>Dernière connexion</th>
          <td>
            <?php echo $utilisateur->connexion; ?>
          </td>
        </tr>
        <?php endif; ?>
        <tr>
          <th>Création du compte</th>
          <td>
            <?php echo $utilisateur->creation.' <span class="label" >'.floor((time()-strtotime($utilisateur->creation))/86400).' jours</span>'; ?>
          </td>
        </tr>
        <tr>
          <th>Adresse</th>
          <td>
            <?php echo $utilisateur->adresse; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <a class="btn btn-info" id="contactutilisateur" style="float:right;" data-id="<?php echo $utilisateur->idutilisateur ?>"><i class="icon-envelope icon-white"></i> Contacter membre</a>
    <h2>Projets</h2>
    <table class="table table-striped"><tbody>
      <?php
        if(!empty($projets)) {
          foreach($projets as $p){
            echo '<tr><td>';
            if(empty($p->image)){
              echo '<img width="50" height="50" src="'.site_url("img/projet_vide.png").'" />';
            } else {
              echo '<img width="50" height="50" src="'.site_url("uploads/profil/projet/".$p->image).'" />';
            }
            if(!empty($mesprojets) && in_array($p->idprojet, $mesprojets)) {
              echo' <a href="'.site_url('projet/accueil/'.$p->idprojet).'">'.$p->nom.'</a>';
            } else {
              echo " ".$p->nom;
            }
            
            echo '</td></tr>';
          }  
        } else {
          echo '<tr><td>Aucun projet.</td></tr>';
        }
       ?>
     </tbody></table>
	
    </div>
  </div>

  <div class="modal hide fade" id="modalmsg">
    <form id="form-message" action="<?php echo site_url("messagerie/contacter_util"); ?>" method="post">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Contacter utilisateur <small> <?php echo $utilisateur->nom ?></small></h3>
    </div>
    <div class="modal-body">
      <input type="hidden" name="idDest" id="idDest" value="<?php echo $utilisateur->idutilisateur ?>" />
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