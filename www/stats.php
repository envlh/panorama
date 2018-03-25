<?php

require '../inc/load.inc.php';

user::checkIsConnected();

define('PAGE_TITLE', 'Statistiques');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

$stats = nain::getStats();
echo '<table class="stats"><tr><th></th><th>Total</th><th>Localisés</th></tr>
<tr><th>Nains</th><td>'.$stats->nains.'</td><td>'.$stats->nains_with_position.'</td></tr>
<tr><th>Nains récents</th><td>'.$stats->recent_nains.'</td><td>'.$stats->recent_nains_with_position.'</td></tr>
<tr><th>Objets</th><td colspan="2">'.$stats->objets.'</td></tr>
<tr><th>Objets récents</th><td colspan="2">'.$stats->recent_objets.'</td></tr>
</table>
<p>Récents = vus il y a moins d\'une semaine.</p>';

require '../inc/footer.inc.php';

?>