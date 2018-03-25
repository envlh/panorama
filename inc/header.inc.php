<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php if (defined('PAGE_TITLE') && !empty(PAGE_TITLE)): echo htmlentities(PAGE_TITLE); else: echo htmlentities(SITE_TITLE); endif; echo ' — '.htmlentities(SITE_SHORT_TITLE) ?></title>
    <link rel="stylesheet" type="text/css" href="static/style.css" />
    <link rel="shortcut icon" href="<?php echo SITE_DIR; ?>favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<div id="menu">
    <ul>
        <?php if (user::getId() !== null): ?>
            <li><img src="<?php echo SITE_STATIC_DIR; ?>img/logo.png" alt="" /> <a href="<?php echo SITE_DIR; ?>maps.php">Panorama</a></li>
            <li><a href="<?php echo SITE_DIR; ?>search.php">Recherche</a></li>
            <li><a href="<?php echo SITE_DIR; ?>update.php">Mise à jour</a></li>
            <li><a href="<?php echo SITE_DIR; ?>user.php?id=<?php echo user::getId(); ?>">Mon compte</a><?php if (!empty(user::getNain())): echo ' / <a href="nain.php?id='.user::getNain().'">Mon nain</a>'; endif; ?></li>
            <li><a href="<?php echo SITE_DIR; ?>users.php">Utilisateurs</a></li>
            <li><a href="<?php echo SITE_DIR; ?>stats.php">Statistiques</a></li>
            <?php if (user::isAdmin()): ?>
                <li><a href="<?php echo SITE_DIR; ?>configuration.php">Configuration</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_DIR; ?>logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><img src="<?php echo SITE_STATIC_DIR; ?>img/logo.png" alt="" /> <a href="<?php echo SITE_DIR; ?>">LPDC</a></li>
        <?php endif; ?>
    </ul>
</div>
<div id="content">