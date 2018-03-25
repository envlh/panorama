<?php

require '../inc/load.inc.php';

user::checkIsConnected();
user::checkHasUpdatedRecently();

define('PAGE_TITLE', 'Panorama');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

$maps = nain::getMaps(true);
$panorama = array();
$panoramaOut = array();
$maxX = 0;
$maxY = 0;
foreach ($maps as $map) {
    if (!empty($map->x) && !empty($map->y)) {
        $panorama[$map->x][$map->y] = $map;
        $maxX = max($maxX, $map->x);
        $maxY = max($maxY, $map->y);
    } else {
        $panoramaOut[] = $map;
    }
}
if (count($panorama) >= 1) {
    echo '<h2>Mondes</h2>
    <table class="panorama">';
    for ($n = 1; $n <= $maxY; $n++) {
        echo '<tr>';
        for ($m = 1; $m <= $maxX; $m++) {
            echo '<td>';
            if (isset($panorama[$m][$n])) {
                $map = $panorama[$m][$n];
                echo '<h3><a href="map.php?id='.$map->id.'">'.htmlentities($map->name).'</a></h3>';
                display::map($map);
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}
if (count($panoramaOut) >= 1) {
    echo '<h2>Autres mondes</h2>';
    foreach ($panoramaOut as $map) {
        echo '<h3><a href="map.php?id='.$map->id.'">'.htmlentities($map->name).'</a></h3>';
        display::map($map);
    }
}

$updates = nain::getLastUpdates();
if (count($updates) >= 1) {
    echo '<h2>Dernières détections</h2>
    <table class="updates"><tr><th>Monde</th><th>X</th><th>Y</th><th>Portée</th><th>Détection</th></tr>'."\n";
    foreach ($updates as $update) {
        echo '<tr><td class="string"><a href="map.php?id='.$update->map_id.'">'.htmlentities($update->map_name).'</a></td><td>'.$update->x.'</td><td>'.$update->y.'</td><td>'.$update->range.'</td><td class="string">'.display::maj($update->date, $update->user_id, $update->user_login, $update->user_nain, $update->user_nain_name).'</td></tr>'."\n";
    }
    echo '</table>';
}

require '../inc/footer.inc.php';

?>