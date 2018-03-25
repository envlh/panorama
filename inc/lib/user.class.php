<?php

class user {
	
	private static $id = null;
	public static function getId() {
		return self::$id;
	}
	
	private static $isAdmin = false;
	public static function isAdmin() {
		return self::$isAdmin;
	}
	
	private static $nain = null;
	public static function getNain() {
		return self::$nain;
	}
	
	public static function validateSession() {
		// invalid session
		if (empty($_COOKIE['user_id']) || empty($_COOKIE['session']) || !is_id($_COOKIE['user_id']) || !preg_match('/^[0-9a-f]{40}$/', $_COOKIE['session'])) {
			return;
		}
		// unkonwn id
		$res = db::query('SELECT `user`.`password`, `user`.`is_active`, `user`.`is_admin`, `nain`.`id` AS `nain` FROM `user` LEFT JOIN `nain` ON (`user`.`nain` = `nain`.`id`) WHERE `user`.`id` = '.$_COOKIE['user_id'].' FOR UPDATE');
		if ($res->num_rows === 0) {
			return;
		}
		$user = $res->fetch_object();
        // inactive
		if ($user->is_active != 1) {
			return;
		}
		// invalid session
		if ($_COOKIE['session'] != self::hash($_COOKIE['user_id'], $user->password, SALT_SESSION_COOKIE)) {
			return;
		}
		// session ok
        db::query('UPDATE `user` SET `last_connection` = NOW() WHERE `id` = '.$_COOKIE['user_id']);
        db::commit();
		self::$id = $_COOKIE['user_id'];
		self::$isAdmin = ($user->is_admin == 1);
		self::$nain = $user->nain;
	}
	
	public static function checkIsConnected() {
		if (self::getId() === null) {
            require '../inc/header.inc.php';
            echo '<p><a href="'.SITE_DIR.'">Connectez-vous</a> pour accéder à cette page.</p>';
            require '../inc/footer.inc.php';
            exit;
		}
	}
    
	public static function checkHasUpdatedRecently() {
		self::checkIsConnected();
        if (self::$id == 1) {
            return;
        }
        $res = db::query('SELECT UNIX_TIMESTAMP(MAX(`date`)) AS `date` FROM `update` WHERE `user` = '.self::getId());
		if (($res->num_rows === 1) && (time() - 86400 * 3 < $res->fetch_object()->date)) {
            return;
		}
        require '../inc/header.inc.php';
        echo '<p><a href="'.SITE_DIR.'update.php">Mettez à jour</a> pour accéder à cette page.</p>';
        require '../inc/footer.inc.php';
        exit;
	}
    
	public static function checkIsAdmin() {
        self::checkIsConnected();
		if (self::isAdmin() === false) {
            require '../inc/header.inc.php';
            echo '<p>Il faut être administrateur pour accéder à cette page.</p>';
            require '../inc/footer.inc.php';
            exit;
		}
	}
	
	public static function login($id, $password) {
		$time = time() + 86400 * 30;
		setcookie('user_id', $id, $time, COOKIE_DIR);
		setcookie('session', self::hash($id, $password, SALT_SESSION_COOKIE), $time, COOKIE_DIR);
	}
	
	public static function logout() {
		setcookie('user_id', '', 0, COOKIE_DIR);
		setcookie('session', '', 0, COOKIE_DIR);
	}
    
	public static function hash($id, $password, $salt) {
		return sha1($id.$password.$salt);
	}
    
    public static function addUser($login) {
        db::query('INSERT INTO `user`(`login`, `password`, `is_active`, `is_admin`) VALUES(\''.db::sec($login).'\', \'\', 1, 0)');
        $id = db::insert_id();
		return self::updateUserPassword($id);
	}
    
    public static function updateUserPassword($id) {
        $password = self::generatePassword();
        $hash = self::hash($id, $password, SALT_USER_PASSWORD);
        db::query('UPDATE `user` SET `password` = \''.$hash.'\' WHERE `id` = '.$id);
		return $password;
	}
    
    public static function updateUserOptions($id, $login, $nain, $is_active, $is_admin) {
		db::query('UPDATE `user` SET `login` = \''.db::sec($login).'\', nain = '.$nain.', is_active = '.$is_active.', is_admin = '.$is_admin.' WHERE `id` = '.$id);
	}
    
    function generatePassword($length = 8) {
        $result = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($chars) - 1);
            $result .= substr($chars, $index, 1);
        }
        return $result;
    }
    
	public static function getUserById($id) {
		$res = db::query('SELECT * FROM `user` WHERE `id` = '.$id);
		if ($res->num_rows == 0) {
			return null;
		}
		return $res->fetch_object();
	}
    
	public static function getUserByLogin($login) {
		$res = db::query('SELECT * FROM `user` WHERE `login` = \''.db::sec($login).'\'');
		if ($res->num_rows == 0) {
			return null;
		}
		return $res->fetch_object();
	}
    
    public static function getUsers($sort) {
        $users = array();
        $orderBy = '';
        switch ($sort) {
            case 'login':
                $orderBy = '`user`.`login` ASC';
            break;
            case 'nain':
                $orderBy = 'ISNULL(`user_nain`.`id`), `user_nain_name`.`name` ASC, `user_nain`.`id` ASC, `user`.`login` ASC';
            break;
            case 'last_connection':
                $orderBy = '`user`.`last_connection` DESC, `user`.`login` ASC';
            break;
            case 'last_update':
                $orderBy = '`user`.`last_update` DESC, `user`.`login` ASC';
            break;
            case 'position':
            default:
                $orderBy = 'ISNULL(`map`.`id`), `map`.`name` ASC, `user_nain`.`last_x` ASC, `user_nain`.`last_y` ASC, `user_nain_name`.`name` ASC, `user`.`login` ASC';
        }
        $res = db::query('SELECT `user`.`id`, `user`.`login`, `user`.`is_active`, `user`.`is_admin`, `user`.`last_connection`, `user`.`last_update`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name`, `user_nain`.`last_x`, `user_nain`.`last_y` FROM `user` LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) LEFT JOIN `map` ON (`user_nain`.`last_map` = `map`.`id`) ORDER BY '.$orderBy);
        while ($user = $res->fetch_object()) {
            $users[] = $user;
        }
        return $users;
    }
	
}

?>