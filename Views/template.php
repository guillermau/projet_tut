<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/i/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php echo $title; ?></title>

  <!-- Mobile viewport optimized: h5bp.com/viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <link rel="stylesheet" href="<?php echo site_url("css/boilerplate.css"); ?>">
  <link rel="stylesheet" href="<?php echo site_url("css/bootstrap.css"); ?>">
  <link rel="stylesheet" href="<?php echo site_url("css/bootstrap-responsive.css"); ?>">
  <link rel="stylesheet" href="<?php echo site_url("css/south-street/jquery-ui-1.9.1.custom.min.css"); ?>">
  <link rel="stylesheet" href="<?php echo site_url("css/style.css"); ?>">
  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except this Modernizr.
       Modernizr enables HTML5 elements & feature detects for optimal performance. -->
  <script src="<?php echo site_url("js/libs/modernizr-2.5.3.min.js"); ?>"></script>
</head>
<body>
  <!--[if lt IE 8]><p class=chromeframe>Votre navigateur est <em>ancien!</em> <a href="http://browsehappy.com/?locale=fr/">Changez à un autre navigateur plus actuel</a> ou <a href="http://www.google.com/chromeframe/?redirect=true">installez Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
  <div class="navbar navbar-fixed-top" role="navigation">

    <div class="navbar-inner">
      <div class="container">
        <?php if($this->profil_model->verifier_connexion()): ?>   
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
   
        <a class="brand" href="<?php echo site_url("mes-projets") ?>">
          Entrepôt de données Écologiques
        </a>
        <div class="nav-collapse">
          <ul class="nav">
            <?php if($this->profil_model->verifier_superAdmin()): ?>
            <li <?php if($sub == "admin") echo 'class="active"';?>><a href="<?php echo site_url("administration"); ?>">Administration</a></li>
            <li <?php if($sub == "accueil" || $sub == "projets") echo 'class="active"';?>><a href="<?php echo site_url("mes-projets"); ?>">Projets</a></li>
            <?php else: ?>
            <li <?php if($sub == "accueil" || $sub == "projets") echo 'class="active"';?>><a href="<?php echo site_url("mes-projets"); ?>">Mes Projets</a></li>
            <li <?php if($sub == "documents") echo 'class="active"';?>><a href="<?php echo site_url("mes-documents");?>">Mes Documents</a></li>
            <li class="divider-vertical"></li>
            <li <?php if($sub == "compte") echo 'class="active"';?>><a href="<?php echo site_url("mon-compte");?>">Mon Compte</a></li>
            <?php endif; ?>
            <li <?php if($sub == "aide") echo 'class="active"';?>><a href="<?php echo site_url("aide");?>">Aide</a></li>
            </ul>
              <ul class="nav pull-right">
                <li <?php if($sub == "utilisateur") echo 'class="active"';?>><a href="<?php echo site_url("mon-compte");?>"><?php echo $this->session->userdata("nom"); ?></a></li>
                <li class="divider-vertical"></li>
                <li><a href="<?php echo site_url("session/deconnexion");?>">Déconnexion</a></li>
              </ul>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <header role="banner">
    <a href="<?php echo site_url("mes-projets") ?>">
    <img class="logo_lbbe" src="<?php echo site_url("img/lbbe.png");?>" />
    <h1>Entrepôt de Données Écologiques</h1>
    </a>
  </header>

  <?php echo $content; ?>

  <footer>

  </footer>


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo site_url("js/libs/jquery-1.7.1.min.js");?>"><\/script>')</script>
  <script src="<?php echo site_url("js/libs/jquery-ui-1.9.1.custom.min.js");?>"></script>

  <script src="<?php echo site_url("js/bootstrap.js");?>"></script>

  <script type="text/javascript">var site = "<?php echo site_url(); ?>";</script>
  <script src="<?php echo site_url("js/plugins.js");?>"></script>
  <script src="<?php echo site_url("js/script.js");?>"></script>
  <!-- end scripts -->
</body>
</html>