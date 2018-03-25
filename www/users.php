<?php

require '../inc/load.inc.php';

user::checkIsConnected();

define('PAGE_TITLE', 'Utilisateurs');
require '../inc/header.inc.php';
echo '<h1>'.htmlentities(PAGE_TITLE).'</h1>';

if (user::isAdmin() && !empty($_POST['login'])) {
    $password = user::addUser($_POST['login']);
    db::commit();
    echo '<p>Compte <strong>'.htmlentities($_POST['login']).'</strong> créé. Mot de passe  : <em>'.$password.'</em></p>';
}

$users = user::getUsers(@$_GET['sort']);
echo '<h2>Liste ('.count($users).')</h2>
<table class="users"><tr><th><a href="'.SITE_DIR.'users.php?sort=login">Identifiant</a></th><th><a href="'.SITE_DIR.'users.php?sort=nain">Nain</a></th><th><a href="'.SITE_DIR.'users.php?sort=position">Monde</a></th><th>X</th><th>Y</th><th>Actif</th><th>Admin</th><th><a href="'.SITE_DIR.'users.php?sort=last_connection">Dernière connexion</a></th><th><a href="'.SITE_DIR.'users.php?sort=last_update">Dernière détection</a></th></tr>'."\n";
foreach ($users as $user) {
    echo '<tr><td class="string">'.display::user($user->id, $user->login).'</td><td class="string">';
    if (!empty($user->user_nain)) {
        echo display::nain($user->user_nain, $user->user_nain_name).'</td><td class="string">';
        if (!empty($user->map_id)) {
            echo '<a href="map.php?id='.$user->map_id.'">'.htmlentities($user->map_name).'</a></td><td>'.$user->last_x.'</td><td>'.$user->last_y;
        } else {
            echo '</td><td></td><td>';
        }
    } else {
        echo '<em>inconnu</em></td><td></td><td></td><td>';
    }
    echo '</td><td>'.($user->is_active == 1 ? 'oui' : '<strong>non</strong>').'</td><td>'.($user->is_admin == 1 ? '<strong>oui</strong>' : 'non').'</td><td class="string">'.display::sincedatetime($user->last_connection).'</td><td class="string">'.display::sincedatetime($user->last_update).'</td></tr>'."\n";
}
echo '</table>';

if (user::isAdmin()) {
    echo '<h2>Ajouter un utilisateur</h2>
    <form action="'.SITE_DIR.'users.php" method="post">
        <p><input type="text" name="login" /> <input type="submit" value="Ajouter" /></p>
    </form>';
}

require '../inc/footer.inc.php';

?>