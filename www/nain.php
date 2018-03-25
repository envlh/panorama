<?php

require '../inc/load.inc.php';

user::checkIsConnected();
user::checkHasUpdatedRecently();

if (empty($_GET['id']) || !is_id($_GET['id']) || (($nain = nain::getNain($_GET['id'])) === null)) {
    if (is_id(@$_GET['id'])) {
        define('PAGE_TITLE', 'Nain #'.$_GET['id']);
    } else {
        define('PAGE_TITLE', 'Nain');
    }
    require '../inc/header.inc.php';
    echo '<h1>'.htmlentities(PAGE_TITLE).'</h1><p>Ce nain est nainconnu dans la matrice LPDC.</p>';
    require '../inc/footer.inc.php';
    exit;
}

if (isset($_POST['note']) && isset($_POST['hp_min']) && isset($_POST['hp_max']) && isset($_POST['bourrin']) && isset($_POST['sniper'])) {
    $hp_min = is_id($_POST['hp_min']) ? $_POST['hp_min'] : 'NULL';
    $hp_max = is_id($_POST['hp_max']) ? $_POST['hp_max'] : 'NULL';
    $bourrin = preg_match('/^[0-9]+(\\.[0-9]+)?$/', $_POST['bourrin']) ? $_POST['bourrin'] : 'NULL';
    $sniper = preg_match('/^[0-9]+(\\.[0-9]+)?$/', $_POST['sniper']) ? $_POST['sniper'] : 'NULL';
    nain::updateNote($nain->id, $_POST['note'], $hp_min, $hp_max, $bourrin, $sniper);
    db::commit();
    header('Location: '.SITE_DIR.'nain.php?id='.$nain->id);
    exit;
}

define('PAGE_TITLE', $nain->name);
require '../inc/header.inc.php';
echo '<h1><img src="'.SITE_STATIC_DIR.'img/'.$nain->path.'" alt="" class="avatar" /> '.htmlentities(PAGE_TITLE).'</h1>';

echo '<h2>Caractéristiques</h2>
<ul>
    <li>Barbe : '.nf_dec($nain->last_level / 100).' cm</li>
    <li>Côté : '.display::side($nain->last_side).'</li>
    <li>Guilde : '.(!empty($nain->guild_id) ? '<a href="search.php?guild='.$nain->guild_id.'" style="color: #'.htmlentities($nain->guild_color).';">'.htmlentities($nain->guild_name).'</a>' : '<em>aucune</em>').'</li>
    <li>Tag : '.(!empty($nain->tag) ? htmlentities($nain->tag) : '<em>aucun</em>').'</li>
    <li>Mort : '.(!empty($nain->last_death) ? display::since($nain->last_death).' ('.display::date($nain->last_death).')' : '<em>inconnue</em>').'</li>
</ul>';

echo '<h2>Informations</h2>
<form action="'.SITE_DIR.'nain.php?id='.$nain->id.'" method="post">
<p><input type="text" name="hp_min" value="'.(!empty($nain->hp_min) ? $nain->hp_min : '').'" class="hp"> ≤ PV ≤ <input type="text" name="hp_max" value="'.(!empty($nain->hp_max) ? $nain->hp_max : '').'" class="hp"> ; Bourrin × <input type="text" name="bourrin" value="'.(!empty($nain->bourrin) ? ($nain->bourrin + 0) : '').'" class="hp"> ; Sniper × <input type="text" name="sniper" value="'.(!empty($nain->sniper) ? ($nain->sniper + 0) : '').'" class="hp"></p>
<p><label for="note">Commentaire :</label><br /><textarea id="note" name="note">'.htmlentities($nain->note).'</textarea></p>
<p><input type="submit" value="Mettre à jour" /></p>
</form>';

echo '<h2>Dernière détections</h2>';
if ($nain->last_x === null) {
    echo '<p>'.htmlentities($nain->name).' a disparu de sa dernière position connue :</p><ul>
        <li>détection '.display::maj($nain->date, $nain->user_id, $nain->user_login, $nain->user_nain, $nain->user_nain_name).'</li>
        <li>dernière mort '.(!empty($nain->last_death) ? display::since($nain->last_death).' ('.display::date($nain->last_death).')' : 'inconnue').'</li>
    </ul>';
}
display::nains($nain->detections);

require '../inc/footer.inc.php';

?>