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
      <li class="active"><a href="<?php echo site_url("projet/accueil/".$projet->idprojet); ?>"><i class="icon-home icon-white"></i> Accueil</a></li>
      <li><a href="<?php echo site_url("projet/documents/".$projet->idprojet); ?>"><i class="icon-file"></i> Documents</a></li>
      <li><a href="<?php echo site_url("projet/membres/".$projet->idprojet); ?>"><i class="icon-user"></i> Membres</a></li>
      <hr/>
      <?php if(!empty($isadmin)): ?>
        <li><a href="<?php echo site_url("projet/gestion/".$projet->idprojet); ?>"><i class="icon-wrench"></i> Gestion du projet</a></li>
      <?php endif; ?>
    </ul>
  </div>

  <div role="main" id="main">
    <div class="page-header">
      <h1><?php echo $projet->nom;?><small> Accueil</small></h1>
    </div>
    <div class="well">
      <h2>Description</h2>
      <?php echo $projet->description; ?>
      <hr/>
      <h2>Statistiques du projet</h2>
      <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th>Date de création</th>
          <td><?php echo $projet->creation ?></td>
        </tr>
        <tr>
          <th>Nombre de documents</th>
          <td><?php echo $nbdocs; ?> documents</td>
        </tr>
        <tr>
          <th>Nombre de membres</th>
          <td><?php echo $nbmembres ?> membres <span class="label label-important"><?php echo $nbinvites ?> invités</span></td>
        </tr>
      </tbody>
    </table>
    <hr/>
      <h3>Tags</h3>
      <ul class="breadcrumb">
      <?php
        if(!empty($projet->tags)) {
          $liste_tags = ''; 
          foreach($projet->tags as $t) {
            $liste_tags .= '<li>'.$t->tag.'<span class="divider">,</span></li>';
          }
          echo substr($liste_tags, 0,strlen($liste_tags)-35);
        }
      ?>
      </ul>
    <hr/>
    <strong>Groupes : </strong>
      <?php
        $liste_groupes = ''; 
        foreach($groupes as $g) {
          $liste_groupes .= ', '.$g->type;
        }
        echo substr($liste_groupes, 2);
      ?>
    </div>
  </div>