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
    <div class="page-header">
      <h1>Mon Compte</h1>
    </div>
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
      <?php 
      if($this->session->flashdata('succes')) {
        echo '<div class="alert alert-success">';
        echo $this->session->flashdata('succes');
        echo '</div>';
      }
      if($this->session->flashdata('echec')) {
        echo '<div class="alert alert-error">';
        echo $this->session->flashdata('error');
        echo '</div>';
      }
      ?>
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
        <tr>
          <th>Dernière connexion</th>
          <td>
            <?php echo $utilisateur->connexion; ?>
          </td>
        </tr>
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
    <a class="btn" href="<?php echo site_url("mon-compte/modifier") ?>">Modifier mes données</a>
    <a class="btn btn-info" href="<?php echo site_url("mon-compte/modifier-image") ?>">Modifier image de profil</a>
    <a class="btn btn-danger" href="<?php echo site_url("mon-compte/modifier-mdp") ?>">Modifier mot de passe</a>
	
    </div>
  </div>