<?php

require '../inc/load.inc.php';

if (user::getId() !== null) {
    header('Location: '.SITE_DIR.'maps.php');
    exit;
}

$error = '';
if (!empty($_POST['login']) && !empty($_POST['password'])) {
    $user = user::getUserByLogin($_POST['login']);
    if (($user == null) || (user::hash($user->id, $_POST['password'], SALT_USER_PASSWORD) !== $user->password)) {
        $error = 'Identifiant ou mot de passe incorrect.';
    }
    elseif ($user->is_active != 1) {
        $error = 'Compte désactivé.';
    }
    else {
        user::login($user->id, $user->password);
        header('Location: '.SITE_DIR.'update.php');
        exit;
    }
}

require '../inc/header.inc.php';
echo '<h1>'.htmlentities(SITE_TITLE).'</h1>';

if (!empty($error)) {
    echo '<p>'.htmlentities($error).'</p>';
}

if (user::getId() == null) {
    echo '<form method="post" action="'.SITE_DIR.'">
    <p><label for="login">Identifiant :</label><br /><input type="text" id="login" name="login" /></p>
    <p><label for="password">Mot de passe :</label><br /><input type="password" id="password" name="password" /></p>
    <p><input type="submit" value="Se connecter" />
    </form>';
}

require '../inc/footer.inc.php';

?>