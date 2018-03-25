<?php

require '../inc/load.inc.php';

user::checkIsAdmin();

define('PAGE_TITLE', 'Configuration');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

echo '<h2>Disposition des mondes</h2>';
if (isset($_POST['id']) && isset($_POST['x']) && isset($_POST['y'])) {
    for ($i = 0; $i < count($_POST['id']); $i++) {
        if (is_id($_POST['id'][$i])) {
            $x = is_id($_POST['x'][$i]) ? $_POST['x'][$i] : 'NULL';
            $y = is_id($_POST['y'][$i]) ? $_POST['y'][$i] : 'NULL';
            nain::updateMap($_POST['id'][$i], $x, $y);
        }
    }
    db::commit();
    echo '<p>Disposition modifiée.</p>';
}
$maps = nain::getMaps(true);
echo '<form action="configuration.php" method="post">
<table class="maps"><tr><th>Nom</th><th>X</th><th>Y</th></tr>'."\n";
foreach ($maps as $map) {
    echo '<tr><td><input type="hidden" name="id[]" value="'.$map->id.'" />'.htmlentities($map->name).'</td><td><input type="text" name="x[]" value="'.$map->x.'" /></td><td><input type="text" name="y[]" value="'.$map->y.'" /></td>';
}
echo '</table>
<p><input type="submit" value="Modifier" /></p>
</form>';

require '../inc/footer.inc.php';

?>