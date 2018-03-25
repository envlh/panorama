<?php

class display {
    
    private static $days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
    private static $months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
    
    public static function side($side) {
        switch ($side) {
            case 1:
                return 'brave';
            break;
            case 2:
                return 'sadique';
            break;
            case 0:
            case 3:
                return 'rampant';
            break;
            default:
                return '';
        }
    }
    
    public static function datetime($date) {
        if (empty($date)) {
            return '';
        }
        $time = strtotime($date);
        return self::date($date).' à '.date('H:i', $time);
    }
    
    public static function date($date) {
        if (empty($date)) {
            return '';
        }
        $time = strtotime($date);
        return self::$days[date('N', $time) - 1].' '.date('j', $time).' '.self::$months[date('n', $time) - 1].' '.date('Y', $time);
    }
    
    public static function since($from) {
        if ($from === null) {
            return 'jamais';
        }
        $dateFrom = new DateTime(substr($from, 0, 10));
        $dateTo = new DateTime(date('Y-m-d'));
        $interval = $dateFrom->diff($dateTo);
        $days = $interval->format('%a');
        if ($days == 0) {
            return 'aujourd\'hui';
        } elseif ($days == 1) {
            return 'hier';
        } elseif ($days == 2) {
            return 'avant-hier';
        } else {
            return 'il y a '.$days.' jours';
        }
    }
    
    public static function sincedatetime($date) {
        if ($date === null) {
            return self::since($date);
        } else {
            return self::since($date).' ('.self::datetime($date).')';
        }
    }
    
    public static function map($map, $big = false) {
        $brave = 0;
        $sadique = 0;
        $rampant = 0;
        echo '<table class="map';
        if ($big) {
            echo ' bigMap';
        }
        echo '"><tr><th></th>';
        for ($i = 1; $i <= 22; $i++) {
            echo '<th>'.$i.'</th>';
        }
        echo '</tr>'."\n";
        for ($j = 1; $j <= 8; $j++) {
            echo '<tr><th>'.$j.'</th>';
            for ($i = 1; $i <= 22; $i++) {
                $sum = '';
                $color = 'undefined';
                if (isset($map->squares[$i][$j])) {
                    $color = 'empty';
                    $square = $map->squares[$i][$j];
                    $age = min(15, floor((time() - strtotime($square->date)) / 86400));
                    if ($age > 15) {
                        $color = 'undefined';
                    } else {
                        $color = $age;
                        if (isset($square->brave) || isset($square->sadique) || isset($square->rampant)) {
                            $color = 'mix';
                            $brave += @$square->brave;
                            $sadique += @$square->sadique;
                            $rampant += @$square->rampant;
                            $sum = @$square->brave + @$square->sadique + @$square->rampant;
                            if (isset($square->brave) && !isset($square->sadique) && !isset($square->rampant)) {
                                $color = 'brave';
                            }
                            elseif (!isset($square->brave) && isset($square->sadique) && !isset($square->rampant)) {
                                $color = 'sadique';
                            }
                            elseif (!isset($square->brave) && !isset($square->sadique) && isset($square->rampant)) {
                                $color = 'rampant';
                            }
                        }
                    }
                }
                echo '<td class="square_'.$color.'">'.$sum.'</td>';
            }
            echo '</tr>'."\n";
        }
        echo '</table><p class="caption"><strong>'.$brave.'</strong> brave'.($brave > 1 ? 's' : '').', <strong>'.$sadique.'</strong> sadique'.($sadique > 1 ? 's' : '').', <strong>'.$rampant.'</strong> rampant'.($rampant > 1 ? 's' : '').'</p>';
    }
    
    public static function nains($nains, $last_death = false) {
        if (count($nains) >= 1) {
            echo '<table class="nains"><tr><th>Monde</th><th>X</th><th>Y</th><th></th><th>Guilde</th><th>Nom</th><th>Barbe</th>';
            if ($last_death) {
                echo '<th>Mort</th>';
            }
            echo '<th>Détection</th></tr>'."\n";
            $map = null;
            $x = null;
            $y = null;
            foreach ($nains as $nain) {
                echo '<tr class="'.self::side($nain->side);
                if (($nain->map_id != $map) || ($nain->x != $x) || ($nain->y != $y)) {
                    echo ' separator';
                    $map = $nain->map_id;
                    $x = $nain->x;
                    $y = $nain->y;
                }
                echo '"><td class="string">'.($nain->map_id !== null ? '<a href="map.php?id='.$nain->map_id.'">'.htmlentities($nain->map_name).'</a></td><td>'.$nain->x.'</td><td>'.$nain->y : '</td><td></td><td>').'</td><td><img src="'.SITE_STATIC_DIR.'img/'.$nain->path.'" alt="" class="avatar" /></td><td>'.(!empty($nain->guild_id) ? '<a href="search.php?guild='.$nain->guild_id.'" style="color: #'.htmlentities($nain->guild_color).';">'.htmlentities($nain->guild_name).'</a>' : '').'</td><td class="string"><a href="nain.php?id='.$nain->id.'">'.htmlentities($nain->name).'</a></td><td class="number">'.nf_dec($nain->level / 100).'</td>';
                if ($last_death) {
                    echo '<td class="string">'.(!empty($nain->last_death) ? self::since($nain->last_death).' ('.self::date($nain->last_death).')' : '<em>inconnue</em>').'</td>';
                }
                echo '<td class="string">'.self::maj($nain->date, $nain->user_id, $nain->user_login, $nain->user_nain, $nain->user_nain_name).'</td></tr>'."\n";
            }
            echo '</table>';
        }
    }
    
    public static function maj($date, $user_id, $user_login, $user_nain, $user_nain_name) {
        $r = self::sincedatetime($date).' par '.self::user($user_id, $user_login);
        if (!empty($user_nain)) {
            $r .= ' ['.self::nain($user_nain, $user_nain_name).']';
        }
        return $r;
    }
    
    public static function user($user_id, $user_login) {
        return '<a href="user.php?id='.$user_id.'">'.htmlentities($user_login).'</a>';
    }
    
    public static function nain($user_nain, $user_nain_name) {
        $r = '<a href="nain.php?id='.$user_nain.'">';
        if ($user_nain_name !== null) {
            $r .= htmlentities($user_nain_name);
        } else {
            $r .= $user_nain;
        }
        $r .= '</a>';
        if ($user_nain_name === null) {
            $r .= ' *';
        }
        return $r;
    }
    
}

?>