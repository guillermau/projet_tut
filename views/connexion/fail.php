<div role="main">
    <div class="well" style="width:230px;margin:auto">
    	<?php if($statut == "clos"): ?>

    		<h1>Compte Clos</h1>

    		<p>Le compte associé à l'adresse email:<br/> <?php echo $email; ?> est <strong>clos</strong>.</p>
    		<p>Veuillez contacter les administrateurs pour plus de renseignements.</p>

    	<?php elseif($statut == "bloque"): ?>

    		<h1>Compte Bloqué</h1>

    		<p>Le compte associé à l'adresse email:<br/> <?php echo $email; ?> est <strong>bloqué</strong>.</p>
    		<p>Veuillez contacter les administrateurs pour plus de renseignements.</p>

    	<?php endif; ?>
    </div>
</div>