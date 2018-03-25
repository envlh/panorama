<?php

require '../inc/load.inc.php';

$offset = 0;
$maxOffset = 0;
for ($offset = 0; $offset <= $maxOffset + 50; $offset += 50) {
    // crawl
    $data = file_get_contents('http://www.nainwak.com/scores/index.php?class=killed&start='.$offset);
    // offset
    preg_match_all('/<a href="index\\.php\\?IDS=[0-9a-f]{32}&class=killed&start=([0-9]+)&nain=&nain=">[0-9]+<\\/a>/', $data, $matches);
    $maxOffset = max($maxOffset, $matches[1][count($matches[0]) - 1]);
    // ranks
    preg_match_all('/<span style="">(.*?)<\\\\\/span> \\(([0-9]+) points\\)/', $data, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        db::query('INSERT INTO `killed`(`name`, `date`, `value`) VALUES('.nain::generateNameId(utf8_encode(html_entity_decode($matches[1][$i], ENT_QUOTES, 'ISO-8859-1'))).', NOW(), '.$matches[2][$i].')');
    }
    // pause
    sleep(1);
}
db::commit();

$res = db::query('SELECT `id`, `last_name`, `last_death` FROM `nain`');
while ($nain = $res->fetch_object()) {
    $a = db::query('SELECT `value` FROM `killed` WHERE `name` = '.$nain->last_name.' ORDER BY `date` DESC LIMIT 1');
    if ($a->num_rows === 1) {
        $b = db::query('SELECT DATE(`date`) AS `date` FROM `killed` WHERE `name` = '.$nain->last_name.' AND `value` < '.$a->fetch_object()->value.' ORDER BY `date` DESC LIMIT 1');
        if ($b->num_rows === 1) {
            db::query('UPDATE `nain` SET `last_death` = \''.$b->fetch_object()->date.'\' WHERE `id` = '.$nain->id);
        }
    }
}
db::query('UPDATE `nain` SET `last_map` = NULL, `last_x` = NULL, `last_y` = NULL WHERE `last_date` < `last_death`');
db::commit();

?>