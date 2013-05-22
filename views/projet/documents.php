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
      <li class="active"><a href="<?php echo site_url("projet/documents/".$projet->idprojet); ?>"><i class="icon-file icon-white"></i> Documents</a></li>
      <li><a href="<?php echo site_url("projet/membres/".$projet->idprojet); ?>"><i class="icon-user"></i> Membres</a></li>
      <hr/>
      <?php if(!empty($isadmin)): ?>
        <li><a href="<?php echo site_url("projet/gestion/".$projet->idprojet); ?>"><i class="icon-wrench"></i> Gestion du projet</a></li>
      <?php endif; ?>
    </ul>
  </div>

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
      <h1><?php echo $projet->nom;?><small> Documents</small></h1>
    </div>

    <?php if($this->session->flashdata('erreur')): ?>
          <div class="alert alert-error">
        <?php echo $this->session->flashdata('erreur'); ?>
      </div>
    <?php endif; ?>
    <?php if($this->session->flashdata('succes')): ?>
          <div class="alert alert-success">
        <?php echo $this->session->flashdata('succes'); ?>
      </div>
    <?php endif; ?>
      
    
	<div class="btn-toolbar" style="text-align:right">
		<div class="btn-group">
			<a class="btn" id="liste" href="#"><i class="icon-list"></i></a>
			<a class="btn" id="boite" href="#"><i class="icon-th-large"></i></a>
		</div>
		
		<div class="btn-group">
		<!-- Gestion dossier -->
			<?php if(!empty($droits->upload)): ?>
				
				<button class="btn" id="nouv-rep">Nouveau dossier</button>
				
				
			<?php endif; ?>
			<?php if(!empty($droits->upload) && $emptyrep == true): ?>
				<a id="suppr-rep" href="<?php echo site_url('projet/supprimer-repertoire/'.$idrep) ?>" class="btn btn">Supprimer dossier</a>
			<?php endif; ?>
		</div>
	</div> 
    <form method="post" action="<?php echo site_url('projet/action/')?>"> <!-- Ajout -->
    <input type="hidden" name="idprojet" id="idprojet" value="<?php echo $idprojet; ?>" /> <!-- Ajout -->
    <input type="hidden" name="idrep_courant" id="idrep_courant" value="<?php echo $idrep; ?>" /> <!-- Ajout -->
    
    <?php
        echo '<div class="btn-toolbar" style="text-align:left">';
        echo '<div class="btn-group">';
        
        foreach($arborescence as $nom => $url){
            echo "<a href=".site_url($url)." class=\"btn btn\">$nom</a>";
        }
        echo '</div></div>';
    ?>
   
    <?php 
      // Si aucun dossier ni répertoire n"est trouvé
      if(empty($documents) && empty($repertoires)) {
        echo "Aucun document.";
        echo '</br>';
      }

      // Sinon
      else {
        echo '<ul class="docliste docs">';
        echo '<li class="entete">Nom du document<div class="meta"><span data-meta="proprietaire">Proprietaire</span><br/><span data-meta="date-maj">Date de mise à jour</span> <span data-meta="date-creation" style="padding-left:30px;"> Date de création </span> </div></li>';

        // On liste les dossiers
        foreach($repertoires as $rep) {
            
            echo '<li class="repertoire">'; 
            echo '<input type="checkbox" name="repertoires" value="'.$rep->idprojet.'" />'; // Ajout 
            echo '<a  class="replink" href="'.site_url("projet/documents/".$rep->idprojet."/".$rep->idrepertoire).'">'.$rep->nom.'</a>';
            echo '</li>'; //<br />
            
           
        }

        // Si le dossier a des documents
        if(!empty($documents)) {
          foreach($documents as $doc){
            if(!empty($doc->droits->lecture) && $doc->droits->lecture == true){
              /*if ($doc->idrepertoire == '0')
                $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->chemin_fichier);
              else
                $chemin = site_url("uploads/projets/".$doc->idprojet."/".$doc->idrepertoire."/".$doc->chemin_fichier);*/
                $chemin= site_url("document/telecharger/".$doc->iddocument);

              echo '<li class="'.$doc->type->type.'"><a class="downlink" target="_blank" href="'.$chemin.'"><img src="'.site_url("img/download.png").'" width="16" height="16" /></a>';
              echo '<input type="checkbox" name="documents" value="'.$doc->iddocument.'"/>';  // Ajout
              if($membres[$doc->idutilisateur] != null){
                $user = $membres[$doc->idutilisateur]->nom;
              }else $user = "ERREUR SUPPRIMER FICHIER ET RE-UPLOADER";
              echo '<a href="'.site_url("document/apercu/".$doc->iddocument).'">'.$doc->nom_original.'<div class="meta"><span data-meta="proprietaire">'.$user.'</span><br/><span data-meta="date-maj">'.date("d/m/Y h:i:s",strtotime($doc->maj)).'</span><span data-meta="date-maj">'.date("d/m/Y h:i:s",strtotime($doc->creation)).'</span></div></a></li>';
            }
          }
        }

        echo '</ul>';

      }
    ?>
    </br>
    <button type="submit" name="ordre" value="telecharger" class="btn" id="telecharger"/>Telecharger</button> <!-- Ajout -->
    <button type="submit" name="ordre" class="btn" />Supprimer</button> <!-- Ajout -->
    </form>
    <div class="clearfix"></div>
    <?php if(!empty($droits->upload)): ?>
    <div class="well" id="uparea">
      <form id="upload" action="<?php echo site_url("document/upload/".$projet->idprojet); ?>" method="POST" enctype="multipart/form-data">  
        <label for="multi_fichiers">Envoyer des fichiers: </label>
        <input type="file" accept="" id="multi_fichiers" name="multi_fichiers[]" multiple="multiple" />  
        <input type="hidden" value="<?php echo $this->session->userdata("idutilisateur"); ?>" name="utilisateur" id="utilisateur" />
        <input type="hidden" value="<?php echo $this->session->userdata("cle"); ?>" name="cle" id="cle" />
        <input type="hidden" value="<?php echo $projet->idprojet; ?>" name="projet" id="projet" />
        <input type="hidden" value="<?php echo $idrep; ?>" name="repertoire" id="repertoire" />
        <button class="btn btn-info" type="submit">Envoyer</button>  
      </form>  
      <div id="upinfo">
        
      </div>
      <div class="clearfix"></div>
    </div>
    <?php endif;?>
  </div>
  <div id="modal-meta" class="modal hide fade">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Metadonnées du fichier envoyé</h3>
    </div>
    <div class="modal-body">
      <form id="form-meta" method="post" action="<?php echo site_url("document/modifier/") ?>">
        <label for="description">Description: </label>
        <textarea  class="span5" name="description" id="description"></textarea>
        <label for="tags">Tags: </label>
        <input class="span5" type="text" name="tags" id="tags" placeholder="Tag 1, Tag 2, Tag 3" />
        <input type="hidden" name="iddoc" id="iddoc" value="" />
        <input type="hidden" value="<?php echo $projet->idprojet; ?>" name="idprojet" id="idprojet" />
        <button type="submit" class="btn">Enregistrer</button>
      </form>
    </div>
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

  <!-- Nouveau dossier -->
  <div id="modal-nouv-rep" class="modal hide fade">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3>Nouveau répertoire</h3>
    </div>

    <form id="form-rep" method="post" action="<?php echo site_url("projet/creer-repertoire"); ?>">
      <div class="modal-body">
        <label for="nom">Nom du répertoire: </label>
        <input type="text" class="span5" name="nom" id="nom" />
        <input type="hidden" name="idprojet" id="idprojet" value="<?php echo $idprojet; ?>" />
        <input type="hidden" name="idpere" id="idpere" value="<?php echo $idrep; ?>" />
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Fermer</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
