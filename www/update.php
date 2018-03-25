<?php

require '../inc/load.inc.php';

user::checkIsConnected();

define('PAGE_TITLE', 'Mise à jour');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

if (!empty($_POST['source'])) {
    try {
        echo '<h2>Résultat</h2>';
        $update = nain::update(user::getId(), $_POST['source']);
        file_put_contents('../detections/'.str_pad($update->id, 8, '0', STR_PAD_LEFT).'.txt', $_POST['source']);
        db::commit();
        echo '<p>Vous êtes en <strong>'.$update->x.','.$update->y.'</strong> sur <strong>'.htmlentities($update->mapName).'</strong>.<br />Votre détecteur a une portée d\'au moins <strong>'.$update->range.'</strong> cases.<br />Vous voyez <strong>'.count($update->nains).'</strong> nains de jardin et <strong>'.count($update->objets).'</strong> objets.</p><p><a href="'.SITE_DIR.'map.php?id='.$update->mapId.'">Voir la carte →</a></p>';
    } catch (Exception $e) {
        echo '<p class="error">Impossible de mettre à jour votre détection :<br /><em>'.htmlentities($e->getMessage()).'</em></p>';
    }
}

echo '<h2>Formulaire</h2>
<p>Copiez-collez la source du cadre de votre détection dans le champ ci-dessous puis cliquez sur <em>Mettre à jour</em>.</p>
<form action="'.SITE_DIR.'update.php" method="post">
<p><textarea name="source" rows="10"></textarea></p>
<p><input type="submit" value="Mettre à jour" /></p>
</form>';

require '../inc/footer.inc.php';

?>