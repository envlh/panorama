<?php

class nain {
    
    public static function getStats() {
        $stats = new stdClass;
        $stats->nains = db::query('SELECT COUNT(*) AS `count` FROM `nain`')->fetch_object()->count;
        $stats->nains_with_position = db::query('SELECT COUNT(*) AS `count` FROM `nain` WHERE `last_map` IS NOT NULL')->fetch_object()->count;
        $stats->recent_nains = db::query('SELECT COUNT(*) AS `count` FROM `nain` WHERE `last_date` >= CURDATE() - INTERVAL 7 DAY')->fetch_object()->count;
        $stats->recent_nains_with_position = db::query('SELECT COUNT(*) AS `count` FROM `nain` WHERE `last_map` IS NOT NULL AND `last_date` >= CURDATE() - INTERVAL 7 DAY')->fetch_object()->count;
        $stats->objets = db::query('SELECT COUNT(*) AS `count` FROM `objet`')->fetch_object()->count;
        $stats->recent_objets = db::query('SELECT COUNT(*) AS `count` FROM `objet`, `update` WHERE `objet`.`current_update` = `update`.`id` AND `update`.`date` >= CURDATE() - INTERVAL 7 DAY')->fetch_object()->count;
        return $stats;
    }
    
    public static function getMaps($details = false) {
        $maps = array();
        $res = db::query('SELECT * FROM `map` ORDER BY `name` ASC');
        while ($map = $res->fetch_object()) {
            if ($details) {
                $map->squares = self::getMapSquares($map->id);
            }
            $maps[] = $map;
        }
        return $maps;
    }
    
    public static function getMap($id) {
        $res = db::query('SELECT * FROM `map` WHERE `id` ='.$id);
        if ($res->num_rows !== 1) {
            return null;
        }
        $map = $res->fetch_object();
        $map->squares = self::getMapSquares($map->id);
        $map->nains = self::getMapNains($map->id);
        $map->objets = self::getMapObjets($map->id);
        return $map;
    }
    
    public static function getMapSquares($id) {
        $squares = array();
        $res = db::query('SELECT `square`.`x`, `square`.`y`, `update`.`date` FROM `square`, `update` WHERE `square`.`current_update` = `update`.`id` AND `square`.`map` = '.$id);
        while ($square = $res->fetch_object()) {
            $squares[$square->x][$square->y] = $square;
        }
        $res = db::query('SELECT `last_x`, `last_y`, `last_side`, COUNT(*) AS `count` FROM `nain` WHERE `last_map` = '.$id.' GROUP BY `last_x`, `last_y`, `last_side`');
        while ($square = $res->fetch_object()) {
            $squares[$square->last_x][$square->last_y]->{display::side($square->last_side)} = $square->count;
        }
        return $squares;
    }
    
    public static function getMapNains($id) {
        $nains = array();
        $res = db::query('SELECT `nain`.`id`, `nain`.`last_death`, `nain`.`note`, `nain`.`hp_min`, `nain`.`hp_max`, `nain`.`bourrin`, `nain`.`sniper`, `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `update_nain`.`level`, `update_nain`.`side`, `update_nain`.`x`, `update_nain`.`y`, `image`.`path`, `name`.`name`, `tag`.`tag`, `guild`.`id` AS `guild_id`, `guild`.`name` AS `guild_name`, `guild`.`color` AS `guild_color` FROM `nain` JOIN `update` ON (`nain`.`current_update` = `update`.`id`) JOIN `update_nain` ON (`update_nain`.`update` = `nain`.`current_update` AND `update_nain`.`nain` = `nain`.`id`) JOIN `image` ON (`update_nain`.`image` = `image`.`id`) JOIN `name` ON (`update_nain`.`name` = `name`.`id`) LEFT JOIN `tag` ON (`update_nain`.`tag` = `tag`.`id`) LEFT JOIN `guild` ON (`update_nain`.`guild` = `guild`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `update`.`map` = '.$id.' AND (`nain`.`last_death` IS NULL OR `nain`.`last_death` < `nain`.`last_date`) ORDER BY `update_nain`.`x` ASC, `update_nain`.`y` ASC, `guild`.`name` ASC, `name`.`name` ASC');
        while ($nain = $res->fetch_object()) {
            $nains[] = $nain;
        }
        return $nains;
    }
    
    public static function getMapObjets($id) {
        $objets = array();
        $res = db::query('SELECT `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `image`.`path`, `objet_type`.`name` AS `type`, `objet_name`.`name`, `objet`.`x`, `objet`.`y` FROM `objet` JOIN `update` ON (`objet`.`current_update` = `update`.`id`) JOIN `image` ON (`objet`.`image` = `image`.`id`) JOIN `objet_type` ON (`objet`.`type` = `objet_type`.`id`) JOIN `objet_name` ON (`objet`.`name` = `objet_name`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `objet`.`map` = '.$id.' ORDER BY `objet`.`x` ASC, `objet`.`y` ASC, `objet`.`type` ASC, `objet_name`.`name` ASC');
        while ($objet = $res->fetch_object()) {
            $objets[] = $objet;
        }
        return $objets;
    }
    
    public static function getNain($id) {
        $res = db::query('SELECT `nain`.`id`, `nain`.`last_level`, `nain`.`last_side`, `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `nain`.`last_x`, `nain`.`last_y`, `nain`.`last_death`, `nain`.`note`, `nain`.`hp_min`, `nain`.`hp_max`, `nain`.`bourrin`, `nain`.`sniper`, `image`.`path`, `name`.`name`, `tag`.`tag`, `guild`.`id` AS `guild_id`, `guild`.`name` AS `guild_name`, `guild`.`color` AS `guild_color`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name` FROM `nain` JOIN `name` ON (`nain`.`last_name` = `name`.`id`) JOIN `image` ON (`nain`.`last_image` = `image`.`id`) LEFT JOIN `tag` ON (`nain`.`last_tag` = `tag`.`id`) LEFT JOIN `guild` ON (`nain`.`last_guild` = `guild`.`id`) LEFT JOIN `map` ON (`nain`.`last_map` = `map`.`id`) LEFT JOIN `update` ON (`nain`.`current_update` = `update`.`id`) LEFT JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `nain`.`id` = '.$id);
        if ($res->num_rows !== 1) {
            return null;
        }
        $nain = $res->fetch_object();
        // anciennes positions
        $nain->detections = array();
        $res = db::query('SELECT `nain`.`id`, `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name`, `update_nain`.`level`, `update_nain`.`side`, `update_nain`.`x`, `update_nain`.`y`, `image`.`path`, `name`.`name`, `tag`.`tag`, `guild`.`id` AS `guild_id`, `guild`.`name` AS `guild_name`, `guild`.`color` AS `guild_color` FROM `nain` JOIN `update_nain` ON (`update_nain`.`nain` = `nain`.`id`) JOIN `update` ON (`update_nain`.`update` = `update`.`id`) JOIN `map` ON (`update`.`map` = `map`.`id`) JOIN `image` ON (`update_nain`.`image` = `image`.`id`) JOIN `name` ON (`update_nain`.`name` = `name`.`id`) LEFT JOIN `tag` ON (`update_nain`.`tag` = `tag`.`id`) LEFT JOIN `guild` ON (`update_nain`.`guild` = `guild`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `nain`.`id` = '.$id.' AND `nain`.`id` <> `user`.`nain` ORDER BY `update`.`date` DESC');
        while ($detection = $res->fetch_object()) {
            $nain->detections[] = $detection;
        }
        return $nain;
    }
    
    public static function getUpdatesByUser($userId) {
        $updates = array();
        $res = db::query('SELECT `update`.`id`, `update`.`date`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name`, `update`.`x`, `update`.`y`, `update`.`range` FROM `update` JOIN `map` ON (`update`.`map` = `map`.`id`) WHERE `update`.`user` = '.$userId.' AND `update`.`date` >= CURDATE() - INTERVAL 30 DAY ORDER BY `update`.`date` DESC');
        while ($update = $res->fetch_object()) {
            $updates[] = $update;
        }
        return $updates;
    }
    
    public static function getUpdatesByMap($mapId) {
        $updates = array();
        $res = db::query('SELECT `update`.`id`, `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `update`.`x`, `update`.`y`, `update`.`range`, COUNT(*) AS `count` FROM `update` JOIN `square` ON (`square`.`current_update` = `update`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `update`.`map` = '.$mapId.' GROUP BY `update`.`id` ORDER BY `update`.`date` DESC');
        while ($update = $res->fetch_object()) {
            $updates[] = $update;
        }
        return $updates;
    }
    
    public static function getLastUpdates() {
        $updates = array();
        $res = db::query('SELECT `update`.`id`, `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name`, `update`.`x`, `update`.`y`, `update`.`range` FROM `update` JOIN `user` ON (`update`.`user` = `user`.`id`) JOIN `map` ON (`update`.`map` = `map`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE `update`.`date` >= CURDATE() - INTERVAL 30 DAY ORDER BY `update`.`date` DESC LIMIT 30');
        while ($update = $res->fetch_object()) {
            $updates[] = $update;
        }
        return $updates;
    }
    
    public static function getGuilds() {
        $guilds = array();
        $res = db::query('SELECT `id`, `name` FROM `guild` ORDER BY `name` ASC');
        while ($guild = $res->fetch_object()) {
            $guilds[] = $guild;
        }
        return $guilds;
    }
    
    public static function searchNains($nain, $guildId) {
        $nains = array();
        $conditions = array();
        if (!empty($nain)) {
            $conditions[] = '`name`.`name` LIKE \'%'.db::sec($nain).'%\'';
        }
        if (!empty($guildId)) {
            $conditions[] = '`nain`.`last_guild` = '.$guildId;
        }
        $res = db::query('SELECT `nain`.`id`, `nain`.`last_level` AS `level`, `nain`.`last_side` AS `side`, `nain`.`last_date` AS `date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `nain`.`last_x` AS `x`, `nain`.`last_y` AS `y`, `nain`.`last_death`, `image`.`path`, `name`.`name`, `tag`.`tag`, `guild`.`id` AS `guild_id`, `guild`.`name` AS `guild_name`, `guild`.`color` AS `guild_color`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name` FROM `nain` JOIN `name` ON (`nain`.`last_name` = `name`.`id`) JOIN `image` ON (`nain`.`last_image` = `image`.`id`) LEFT JOIN `tag` ON (`nain`.`last_tag` = `tag`.`id`) LEFT JOIN `guild` ON (`nain`.`last_guild` = `guild`.`id`) LEFT JOIN `map` ON (`nain`.`last_map` = `map`.`id`) JOIN `update` ON (`nain`.`current_update` = `update`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE '.implode(' AND ', $conditions).' ORDER BY `map`.`name` ASC, `nain`.`last_x` ASC, `nain`.`last_y` ASC, `name`.`name` ASC');
        while ($nain = $res->fetch_object()) {
            $nains[] = $nain;
        }
        return $nains;
    }
    
    public static function searchObjets($name, $type) {
        $objets = array();
        $conditions = array();
        if (!empty($name)) {
            $conditions[] = '`objet_name`.`name` LIKE \'%'.db::sec($name).'%\'';
        }
        if (!empty($type)) {
            $conditions[] = '`objet_type`.`code` = \''.db::sec($type).'\'';
        }
        $res = db::query('SELECT `update`.`date`, `user`.`id` AS `user_id`, `user`.`login` AS `user_login`, `user`.`nain` AS `user_nain`, `user_nain_name`.`name` AS `user_nain_name`, `image`.`path`, `objet_type`.`name` AS `type`, `objet_name`.`name`, `map`.`id` AS `map_id`, `map`.`name` AS `map_name`, `objet`.`x`, `objet`.`y` FROM `objet` JOIN `map` ON (`objet`.`map` = `map`.`id`) JOIN `update` ON (`objet`.`current_update` = `update`.`id`) JOIN `image` ON (`objet`.`image` = `image`.`id`) JOIN `objet_type` ON (`objet`.`type` = `objet_type`.`id`) JOIN `objet_name` ON (`objet`.`name` = `objet_name`.`id`) JOIN `user` ON (`update`.`user` = `user`.`id`) LEFT JOIN `nain` AS `user_nain` ON (`user`.`nain` = `user_nain`.`id`) LEFT JOIN `name` AS `user_nain_name` ON (`user_nain`.`last_name` = `user_nain_name`.`id`) WHERE '.implode(' AND ', $conditions).' ORDER BY `map`.`name` ASC, `objet`.`x` ASC, `objet`.`y` ASC, `objet`.`type` ASC, `objet_name`.`name` ASC');
        while ($objet = $res->fetch_object()) {
            $objets[] = $objet;
        }
        return $objets;
    }
    
    public static function generateMapId($name) {
        $res = db::query('SELECT `id` FROM `map` WHERE `name` = \''.db::sec($name).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            return $res->fetch_object()->id;
        } else {
            db::query('INSERT INTO `map`(`name`) VALUES(\''.db::sec($name).'\')');
            $mapId = db::insert_id();
            for ($j = 1; $j <= 8; $j++) {
                for ($i = 1; $i <= 22; $i++) {
                    db::query('INSERT INTO `square`(`map`, `x`, `y`, `current_update`) VALUES('.$mapId.', '.$i.', '.$j.', NULL)');
                }
            }
            return $mapId;
        }
    }
    
    public static function updateMap($id, $x, $y) {
        db::query('UPDATE `map` SET `x` = '.$x.', `y` = '.$y.' WHERE `id` = '.$id);
    }
    
    public static function getObjetTypes() {
        $types = array();
        $res = db::query('SELECT `id`, `code`, `name` FROM `objet_type` ORDER BY `name` ASC');
        while ($type = $res->fetch_object()) {
            $types[$type->code] = $type;
        }
        return $types;
    }
    
    public static function generateObjetNameId($name) {
        $res = db::query('SELECT `id` FROM `objet_name` WHERE `name` = \''.db::sec($name).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            return $res->fetch_object()->id;
        } else {
            db::query('INSERT INTO `objet_name`(`name`) VALUES(\''.db::sec($name).'\')');
            return db::insert_id();
        }
    }
    
    public static function generateNameId($name) {
        $res = db::query('SELECT `id` FROM `name` WHERE `name` = \''.db::sec($name).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            return $res->fetch_object()->id;
        } else {
            db::query('INSERT INTO `name`(`name`) VALUES(\''.db::sec($name).'\')');
            return db::insert_id();
        }
    }
    
    public static function generateTagId($tag) {
        $res = db::query('SELECT `id` FROM `tag` WHERE `tag` = \''.db::sec($tag).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            return $res->fetch_object()->id;
        } else {
            db::query('INSERT INTO `tag`(`tag`) VALUES(\''.db::sec($tag).'\')');
            return db::insert_id();
        }
    }
    
    public static function generateGuildId($name, $color) {
        $res = db::query('SELECT `id` FROM `guild` WHERE `name` = \''.db::sec($name).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            $id = $res->fetch_object()->id;
            db::query('UPDATE `guild` SET `color` = \''.db::sec($color).'\' WHERE `id` = '.$id);
            return $id;
        } else {
            db::query('INSERT INTO `guild`(`name`, `color`) VALUES(\''.db::sec($name).'\', \''.db::sec($color).'\')');
            return db::insert_id();
        }
    }
    
    public static function generateImageId($path) {
        $res = db::query('SELECT `id` FROM `image` WHERE `path` = \''.db::sec($path).'\' FOR UPDATE');
        if ($res->num_rows === 1) {
            $id = $res->fetch_object()->id;
        } elseif (preg_match('/^[a-zA-Z0-9_\\/-]+\\.(png|gif)$/', $path)) {
            db::query('INSERT INTO `image`(`path`) VALUES(\''.db::sec($path).'\')');
            $id = db::insert_id();
        } else {
            throw new Exception('Chemin d\'image non valide : '.$path);
        }
        $newPath = 'static/img/'.$path;
        if (!file_exists($newPath)) {
            file_put_contents($newPath, file_get_contents('http://www.nainwak.com/images/'.$path));
        }
        return $id;
    }
    
    public static function generateNainId($id) {
        $res = db::query('SELECT `id` FROM `nain` WHERE `id` = '.$id.' FOR UPDATE');
        if ($res->num_rows === 1) {
            return $res->fetch_object()->id;
        } else {
            db::query('INSERT INTO `nain`(`id`) VALUES('.$id.')');
            return db::insert_id();
        }
    }
    
    public static function createUpdate($userId, $mapId, $x, $y, $range) {
        db::query('INSERT INTO `update`(`date`, `user`, `map`, `x`, `y`, `range`) VALUES(NOW(), '.$userId.', '.$mapId.', '.$x.', '.$y.', '.$range.')');
        $updateId = db::insert_id();
        db::query('UPDATE `user` SET `last_update` = NOW() WHERE `id` = '.$userId);
        return $updateId;
    }
    
    public static function update($userId, $source) {
        
        // nain qui fait la détection
        if (preg_match('/Position \\(([1-9]|1[0-9]|2[0-2]),([1-8])\\) sur "(.*?)"/', $source, $match) !== 1) {
            throw new Exception('Impossible de déterminer votre position.');
        }
        $mapName = $match[3];
        $x = $match[1];
        $y = $match[2];
        
        $range = 0;
        
        // nains de la détection
        // gavat(id, photo, nom, tag, barbe, classe, cote, distance, x, y, description, attaquer, gifler, estCible)
        $nains = array();
        preg_match_all('/tabavat\\[[0-9]+\\] = \\["([0-9]+)", "(.*?)", "(.*?)", \'(.*?)\', "([0-9]+)", "([0-9]+)", "(.*?)", "([0-9]+)", "([1-9]|1[0-9]|2[0-2])", "([1-8])", "(.*?)", "(.*?)", "(.*?)", "(.*?)"];/', $source, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            if (empty($matches[4][$i])) {
                $tag = 'NULL';
            } else {
                $tag = nain::generateTagId(html_entity_decode(strip_tags($matches[4][$i]), ENT_QUOTES));
            }
            if (preg_match('/<span style="color:#([0-9A-F]{6});">(.*?)<\\/span>/', $matches[4][$i], $match) !== 1) {
                $guild = 'NULL';
            } else {
                $guild = nain::generateGuildId(html_entity_decode($match[2], ENT_QUOTES), $match[1]);
            }
            $nains[] = array(
                'id' => $matches[1][$i],
                'image' => nain::generateImageId($matches[2][$i]),
                'name' => nain::generateNameId($matches[3][$i]),
                'tag' => $tag,
                'guild' => $guild,
                'level' => $matches[5][$i],
                'side' => $matches[6][$i],
                'x' => $matches[9][$i],
                'y' => $matches[10][$i]
            );
            $range = max($range, $matches[8][$i]);
        }
        
        // objets de la détection
        // gobjet(id, photo, nom, distance, x, y, poussiere, prendre)
        $types = nain::getObjetTypes();
        $objets = array();
        preg_match_all('/tabobjet\\[[0-9]+\\] = \\[([0-9]+), "(.*?)", "(.*?)", ([0-9]+), ([1-9]|1[0-9]|2[0-2]), ([1-8]), "(.*?)", ([0-9-]+)\\];/', $source, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            if (!empty($types[$matches[7][$i]])) {
                $type = $types[$matches[7][$i]]->id;
            } else {
                throw new Exception('Objet de type inconnu.');
            }
            $objets[] = array(
                'id' => $matches[1][$i],
                'image' => nain::generateImageId($matches[2][$i]),
                'type' => $type,
                'name' => nain::generateObjetNameId(html_entity_decode($matches[3][$i], ENT_QUOTES)),
                'x' => $matches[5][$i],
                'y' => $matches[6][$i],
                'dust' => $matches[8][$i]
            );
            $range = max($range, $matches[4][$i]);
        }
        
        $mapId = nain::generateMapId($mapName);
        $updateId = nain::createUpdate($userId, $mapId, $x, $y, $range);
        nain::cleanBeforeUpdate($updateId, $userId, $mapId, $x, $y, $range);
        foreach ($nains as $nain) {
            nain::updateNain($updateId, $nain['id'], $nain['image'], $nain['name'], $nain['tag'], $nain['guild'], $nain['level'], $nain['side'], $mapId, $nain['x'], $nain['y']);
        }
        foreach ($objets as $objet) {
            nain::addObjet($updateId, $objet['id'], $objet['image'], $objet['type'], $objet['name'], $mapId, $objet['x'], $objet['y'], $objet['dust']);
        }
        nain::updateSquares($updateId, $mapId, $x, $y, $range);
        
        $update = new StdClass;
        $update->id = $updateId;
        $update->mapName = $mapName;
        $update->mapId = $mapId;
        $update->x = $x;
        $update->y = $y;
        $update->range = $range;
        $update->nains = $nains;
        $update->objets = $objets;
        return $update;
        
    }
    
    public static function cleanBeforeUpdate($updateId, $userId, $mapId, $x, $y, $range) {
        // nains
        db::query('UPDATE `nain`, `update`, `update_nain` SET `nain`.`current_update` = '.$updateId.', `nain`.`last_map` = NULL, `nain`.`last_x` = NULL, `nain`.`last_y` = NULL WHERE `nain`.`current_update` = `update`.`id` AND `update_nain`.`update` = `nain`.`current_update` AND `update_nain`.`nain` = `nain`.`id` AND `update`.`map` = '.$mapId.' AND ROUND(SQRT((`update_nain`.`x` - '.$x.') * (`update_nain`.`x` - '.$x.') + (`update_nain`.`y` - '.$y.') * (`update_nain`.`y` - '.$y.'))) <= '.$range);
        $res = db::query('SELECT `nain`.`id`, `nain`.`last_image`, `nain`.`last_name`, `nain`.`last_tag`, `nain`.`last_guild`, `nain`.`last_level`, `nain`.`last_side` FROM `user`, `nain` WHERE `user`.`id` = '.$userId.' AND `user`.`nain` = `nain`.`id`');
        if ($res->num_rows === 1) {
            $nain = $res->fetch_object();
            $tag = !empty($nain->last_tag) ? $nain->last_tag : 'NULL';
            $guild = !empty($nain->last_guild) ? $nain->last_guild : 'NULL';
            nain::updateNain($updateId, $nain->id, $nain->last_image, $nain->last_name, $tag, $guild, $nain->last_level, $nain->last_side, $mapId, $x, $y);
        }
        // objets
        db::query('DELETE FROM `objet` WHERE `map` = '.$mapId.' AND ROUND(SQRT((`x` - '.$x.') * (`x` - '.$x.') + (`y` - '.$y.') * (`y` - '.$y.'))) <= '.$range);
    }
    
    public static function updateNain($updateId, $nainId, $image, $name, $tag, $guild, $level, $side, $map, $x, $y) {
        self::generateNainId($nainId);
        // màj cache table nain
        db::query('UPDATE `nain` SET `current_update` = '.$updateId.', `last_date` = NOW(), `last_image` = '.$image.', `last_name` = '.$name.', `last_tag` = '.$tag.', `last_guild` = '.$guild.', `last_level` = '.$level.', `last_side` = '.$side.', `last_map` = '.$map.', `last_x` = '.$x.', `last_y` = '.$y.' WHERE `id` = '.$nainId);
        // ajout dans update_nain
        db::query('INSERT INTO `update_nain`(`update`, `nain`, `image`, `name`, `tag`, `guild`, `level`, `side`, `x`, `y`) VALUES('.$updateId.', '.$nainId.', '.$image.', '.$name.', '.$tag.', '.$guild.', '.$level.', '.$side.', '.$x.', '.$y.')');
    }
    
    public static function addObjet($updateId, $objetId, $image, $type, $name, $map, $x, $y, $dust) {
        db::query('INSERT INTO `objet`(`id`, `current_update`, `image`, `type`, `name`, `map`, `x`, `y`, `dust`) VALUES('.$objetId.', '.$updateId.', '.$image.', '.$type.', '.$name.', '.$map.', '.$x.', '.$y.', '.$dust.')');
    }
    
    public static function updateSquares($updateId, $mapId, $x, $y, $range) {
        db::query('UPDATE `square` SET `current_update` = '.$updateId.' WHERE `map` = '.$mapId.' AND ROUND(SQRT((`x` - '.$x.') * (`x` - '.$x.') + (`y` - '.$y.') * (`y` - '.$y.'))) <= '.$range);
    }
    
    public static function updateNote($nainId, $note, $hp_min, $hp_max, $bourrin, $sniper) {
        db::query('UPDATE `nain` SET `note` = '.(!empty($note) ? '\''.db::sec(mb_substr($note, 0, 1000)).'\'' : 'NULL').', `hp_min` = '.$hp_min.', `hp_max` = '.$hp_max.', `bourrin` = '.$bourrin.', `sniper` = '.$sniper.' WHERE `id` = '.$nainId);
    }
    
}

?>