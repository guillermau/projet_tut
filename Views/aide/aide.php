<div role="main">
    <div class="well" style="width:830px;margin:auto">
		<h1 style="text-align: center;text-decoration:bold">Page d'aide</h1>
		 <?php 
			$avant='rien';
			foreach($aide as $ligne){
			if($avant!=$ligne->categorie){
			?></br><h2 style="text-decoration:underline;text-align: center" ><?php echo $ligne->categorie;?></h2></br><?php 
			$avant = $ligne->categorie;
			}
			echo $ligne->contenu;	
			}
		 ?>		 
    </div>
</div>