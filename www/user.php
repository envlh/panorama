<?php

require '../inc/load.inc.php';

user::checkIsConnected();

if (empty($_GET['id']) || !is_id($_GET['id']) || (($user = user::getUserById($_GET['id'])) === null)) {
    define('PAGE_TITLE', 'Utilisateur');
    require '../inc/header.inc.php';
    echo '<h1>'.htmlentities(PAGE_TITLE).'</h1><p>Cet utilisateur n\'existe pas.</p>';
    require '../inc/footer.inc.php';
    exit;
}

define('PAGE_TITLE', 'Utilisateur : '.$user->login);
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

if (user::isAdmin()) {
    echo '<h2>Options</h2>';
    if (!empty($_POST['login'])) {
        user::updateUserOptions($user->id, $_POST['login'], (is_id(@$_POST['nain']) ? $_POST['nain'] : 'NULL'), (@$_POST['is_active'] == 1 ? 1 : 0), (@$_POST['is_admin'] == 1 ? 1 : 0));
        db::commit();
        $user = user::getUserById($user->id);
        echo '<p>Options mises à jour.</p>';
    }
    echo '<form action="user.php?id='.$user->id.'" method="post">
    <p><label for="login">Identifiant du compte :</label><br /><input type="text" name="login" id="login" value="'.htmlentities($user->login).'" /></p>
    <p><label for="nain">Id du nain :</label><br /><input type="text" name="nain" id="nain" value="'.$user->nain.'" /></p>
    <p>
        <input type="checkbox" name="is_active" id="is_active" value="1"'.($user->is_active == 1 ? ' checked="checked"' : '').' /> <label for="is_active">Compte actif</label><br />
        <input type="checkbox" name="is_admin" id="is_admin" value="1"'.($user->is_admin == 1 ? ' checked="checked"' : '').' /> <label for="is_admin">Administrateur</label>
    </p>
    <p><input type="submit" value="Modifier" /></p>
    </form>';
}

if (user::isAdmin() || (user::getId() == $user->id)) {
    echo '<h2>Mot de passe</h2>';
    if (!empty($_POST['action']) && ($_POST['action'] == 'password')) {
        $password = user::updateUserPassword($user->id);
        db::commit();
        echo '<p>Nouveau mot de passe : <em>'.$password.'</em></p>';
    }
    echo '<form action="user.php?id='.$user->id.'" method="post">
    <p><input type="hidden" name="action" value="password" /><input type="submit" value="Générer un nouveau mot de passe" /></p>
    </form>';
    
}

echo '<h2>Dernières détections</h2>';
$updates = nain::getUpdatesByUser($user->id);
if (count($updates) >= 1) {
    echo '<table class="updates"><tr><th>Monde</th><th>X</th><th>Y</th><th>Portée</th><th>Date</th></tr>'."\n";
    foreach ($updates as $update) {
        echo '<tr><td class="string"><a href="map.php?id='.$update->map_id.'">'.htmlentities($update->map_name).'</a></td><td>'.$update->x.'</td><td>'.$update->y.'</td><td>'.$update->range.'</td><td class="string">'.display::sincedatetime($update->date).'</td></tr>'."\n";
    }
    echo '</table>';
} else {
    echo '<p>'.htmlentities($user->login).' n\'a effectué aucune mise à jour au cours des 30 derniers jours.</p>';
}

require '../inc/footer.inc.php';

?>