<?php

class DebugHelper
{

    public static function jdd($object, $die = false)
    {
        echo json_encode($object);
    }

    public static function query_log()
    {
        $total = 0;
        foreach ($_ENV['debug'] as $d) {
            $total += $d['time'];
            echo "<div class='panel panel-warning' style='background:white;color:#3E3E3E;padding:15px;'><strong>{$d['time']}</strong><p>{$d['sql']}</p><br>";
            DebugHelper::pdd($d['bindings']);
            echo "</div>";
        }
        echo "<h3>Total time: " . $total / 1000 . " seconds</h3>";
    }

    public static function pdd($object, $die = false)
    {
        echo "<pre>";
        $var = print_r($object, true);
        $var = str_replace('[', "[<bold style='color:orange;'>", $var);
        $var = str_replace(']', "</bold>]", $var);
        var_dump($var);
        echo "</pre>";
        if ($die) die();
    }

}

?>