<div role="main" id="main">
	<div class="page-header">
		<h1>Administration</h1>
	</div>
	<div class="well">
		<h2>
		Ajouter utilisateurs <span class="label label-important"><?php echo $nbattente; ?> en attente</span>
		</h2>
		
		<table class="table">
			<tr>
				<th> NOM, Prenom</th>
				<th> Mail </th>
                                <th> Justification </th>       
				<th> Date de création </th>
				<th> Ajouter</th>
				<th> Supprimer</th>
			</tr>
		<?php 
			foreach ($utilisateurs as $u) {
                            foreach($justification as $just){
                                if($just->id_user == $u->idutilisateur){
                                echo "<tr>";
				echo '<td>'.$u->nom.', '.$u->prenom.'</td>';
				echo '<td>'.$u->email.'</td>';
                                echo '<td>'.$just->justification.'</td>';
				echo '<td>'.$u->creation.'</td>';
				echo '<td class="sa-ajouter"><a href="'.site_url('administration/accepter_membre/'.$u->idutilisateur).'" class="btn btn-success">Membre</a> <a href="'.site_url('administration/accepter_invite/'.$u->idutilisateur).'" class="btn">Invité</a></td>';
				echo '<td class="sa-ajouter"><a href="'.site_url('administration/refuser_inscription/'.$u->idutilisateur).'" class="btn btn-danger">Supprimer</a></td>';
				echo "</tr>";
                                }
                            }
			}

		?>
		</table>
	</div>
</div>