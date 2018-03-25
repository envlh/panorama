<?php

require '../inc/load.inc.php';

user::checkIsConnected();
user::checkHasUpdatedRecently();

define('PAGE_TITLE', 'Recherche');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

$nainName = !empty($_GET['nainName']) ? $_GET['nainName'] : null;
$guildId = is_id(@$_GET['guild']) ? $_GET['guild'] : null;
$objetName = !empty($_GET['objetName']) ? $_GET['objetName'] : null;
$objetType = !empty($_GET['type']) && preg_match('/^[A-Z]+$/', $_GET['type']) ? $_GET['type'] : null;

echo '<form action="'.SITE_DIR.'search.php" method="get">
<h2>Nains</h2>
<p><label for="nainName">Nom :</label><br /><input type="text" name="nainName" id="nainName" value="'.htmlentities($nainName).'" /></p>
<p><label for="guild">Guilde :</label><br /><select name="guild" id="guild">
<option value=""></option>';
$guilds = nain::getGuilds();
foreach ($guilds as $guild) {
    echo '<option value="'.$guild->id.'"';
    if ($guildId  == $guild->id) {
        echo ' selected="selected"';
    }
    echo '>'.htmlentities($guild->name).'</option>';
}
echo '</select></p>
<h2>Objets</h2>
<p><label for="objetName">Nom :</label><br /><input type="text" name="objetName" id="objetName" value="'.htmlentities($objetName).'" /></p>
<p><label for="type">Type :</label><br /><select name="type" id="type">
<option value=""></option>';
$types = nain::getObjetTypes();
foreach ($types as $code => $type) {
    echo '<option value="'.$code.'"';
    if ($objetType  == $code) {
        echo ' selected="selected"';
    }
    echo '>'.htmlentities($type->name).'</option>';
}
echo '</select></p>
<p><input type="submit" value="Rechercher" /></p>
</form>';

if (($nainName !== null) || ($guildId !== null)) {
    $results = nain::searchNains($nainName, $guildId);
    echo '<h2>Résultats ('.count($results).')</h2>';
    display::nains($results, true);
}
elseif (!empty($objetName) || isset($types[$objetType])) {
    $results = nain::searchObjets($objetName, $objetType);
    echo '<h2>Résultats ('.count($results).')</h2>
    <table class="objets"><tr><th>Monde</th><th>X</th><th>Y</th><th></th><th>Type</th><th>Nom</th><th>Détection</th></tr>'."\n";
    $map = null;
    $x = null;
    $y = null;
    foreach ($results as $objet) {
        echo '<tr';
        if (($objet->map_id != $map) || ($objet->x != $x) || ($objet->y != $y)) {
            echo ' class="separator"';
            $map = $objet->map_id;
            $x = $objet->x;
            $y = $objet->y;
        }
        echo '><td class="string"><a href="map.php?id='.$objet->map_id.'">'.htmlentities($objet->map_name).'</a></td><td>'.$objet->x.'</td><td>'.$objet->y.'</td><td><img src="'.SITE_STATIC_DIR.'img/'.$objet->path.'" alt="" class="avatar" /></td><td>['.htmlentities(mb_substr($objet->type, 0, 1)).']</td><td class="string"><a href="http://www.biblionainwak.com/rechercher/?objet='.urlencode(utf8_decode($objet->name)).'">'.htmlentities($objet->name).'</a></td><td class="string">'.display::maj($objet->date, $objet->user_id, $objet->user_login, $objet->user_nain, $objet->user_nain_name).'</td></tr>'."\n";
    }
    echo '</table>';
}

require '../inc/footer.inc.php';

?>