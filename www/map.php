<?php

require '../inc/load.inc.php';

user::checkIsConnected();
user::checkHasUpdatedRecently();

if (empty($_GET['id']) || !is_id($_GET['id']) || (($map = nain::getMap($_GET['id'])) === null)) {
    define('PAGE_TITLE', 'Monde');
    require '../inc/header.inc.php';
    echo '<h1>'.htmlentities(PAGE_TITLE).'</h1><p>Ce monde n\'existe pas.</p>';
    require '../inc/footer.inc.php';
    exit;
}

define('PAGE_TITLE', $map->name);
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

display::map($map, true);

$updates = nain::getUpdatesByMap($map->id);
if (count($updates) >= 1) {
    echo '<h2>Détections utilisées</h2>
    <table class="updates"><tr><th>X</th><th>Y</th><th>Portée</th><th>Cases</th><th>Détection</th></tr>'."\n";
    foreach ($updates as $update) {
        echo '<tr><td>'.$update->x.'</td><td>'.$update->y.'</td><td>'.$update->range.'</td><td>'.$update->count.'</td><td class="string">'.display::maj($update->date, $update->user_id, $update->user_login, $update->user_nain, $update->user_nain_name).'</td></tr>'."\n";
    }
    echo '</table>';
}

echo '<h2>Nains ('.count($map->nains).')</h2>';
if (count($map->nains) >= 1) {
    echo '<table class="nains"><tr><th>X</th><th>Y</th><th></th><th>Guilde</th><th>Nom</th><th>Barbe</th><th>Mort</th><th>Informations</th><th>Détection</th></tr>'."\n";
    $x = null;
    $y = null;
    foreach ($map->nains as $nain) {
        echo '<tr class="'.display::side($nain->side);
        if (($nain->x != $x) || ($nain->y != $y)) {
            echo ' separator';
            $x = $nain->x;
            $y = $nain->y;
        }
        echo '"><td>'.$nain->x.'</td><td>'.$nain->y.'</td><td><img src="'.SITE_STATIC_DIR.'img/'.$nain->path.'" alt="" class="avatar" /></td><td>'.(!empty($nain->guild_id) ? '<a href="search.php?guild='.$nain->guild_id.'" style="color: #'.htmlentities($nain->guild_color).';">'.htmlentities($nain->guild_name).'</a>' : '').'</td><td class="string"><a href="nain.php?id='.$nain->id.'">'.htmlentities($nain->name).'</a></td><td class="number">'.nf_dec($nain->level / 100).'</td><td class="string">'.(!empty($nain->last_death) ? display::since($nain->last_death).' ('.display::date($nain->last_death).')' : '<em>inconnue</em>').'</td><td class="string">';
        $notes = array();
        if (!empty($nain->hp_min) && !empty($nain->hp_max)) {
            if ($nain->hp_min === $nain->hp_max) {
                $notes[] = $nain->hp_min.' PV';
            } else {
                $notes[] = $nain->hp_min.'~'.$nain->hp_max.' PV';
            }
        } elseif (!empty($nain->hp_min)) {
            $notes[] = '≥ '.$nain->hp_min.' PV';
        } elseif (!empty($nain->hp_max)) {
            $notes[] = '≥ '.$nain->hp_max.' PV';
        }
        if (!empty($nain->bourrin)) {
            $notes[] = 'Bourrin × '.($nain->bourrin + 0);
        }
        if (!empty($nain->sniper)) {
            $notes[] = 'Sniper × '.($nain->sniper + 0);
        }
        if (!empty($nain->note)) {
            $notes[] = htmlentities($nain->note);
        }
        if (count($notes) >= 1) {
            echo implode(' ; ', $notes);
        }
        echo '</td><td class="string">'.display::maj($nain->date, $nain->user_id, $nain->user_login, $nain->user_nain, $nain->user_nain_name).'</td></tr>'."\n";
    }
    echo '</table>';
}

echo '<h2>Objets ('.count($map->objets).')</h2>';
if (count($map->objets) >= 1) {
    echo '<table class="objets"><tr><th>X</th><th>Y</th><th></th><th>Type</th><th>Nom</th><th>Détection</th></tr>'."\n";
    $x = null;
    $y = null;
    foreach ($map->objets as $objet) {
        echo '<tr';
        if (($objet->x != $x) || ($objet->y != $y)) {
            echo ' class="separator"';
            $x = $objet->x;
            $y = $objet->y;
        }
        echo '><td>'.$objet->x.'</td><td>'.$objet->y.'</td><td><img src="'.SITE_STATIC_DIR.'img/'.$objet->path.'" alt="" class="avatar" /></td><td>['.htmlentities(mb_substr($objet->type, 0, 1)).']</td><td class="string"><a href="http://www.biblionainwak.com/rechercher/?objet='.urlencode(utf8_decode($objet->name)).'">'.htmlentities($objet->name).'</a></td><td class="string">'.display::maj($objet->date, $objet->user_id, $objet->user_login, $objet->user_nain, $objet->user_nain_name).'</td></tr>'."\n";
    }
    echo '</table>';
}

require '../inc/footer.inc.php';

?>