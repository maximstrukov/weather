<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_log', 'php_errors.log');

require("config.php");
require("Db.php");
require("Balloon.php");
require("Reading.php");

//reading the files from data folder
if ($handle = opendir('data')) {
    
    $db = new DB();
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo $entry.EOL;
            
            //parsing filename with regular expression
            if (preg_match('/(\d+)-(\w+)-(\w+)-(\w+)\.csv/', $entry, $match)) {
                $balloon = new Balloon($match);
                if ($balloon->save($db->link)) {
                
                    //opening file and extracting readings
                    $f = fopen('data'.DIRECTORY_SEPARATOR.$entry, "r");
                    if ($f) {
                        $l = 0; //current file line number
                        $counter = 0; //counter of saved readings
                        while (($line = fgets($f)) !== false) {
                            $l++;
                            if ($l==1) continue;
                            $vars = explode(",",trim($line));
                            array_unshift($vars, $balloon->flight_number);
                            $reading = new Reading($vars);
                            if ($reading->save($db->link, $l)) $counter++;
                            unset($reading);
                        }
                        if (!feof($f)) {
                            echo "Error: unexpected fgets() fail".EOL;
                        }
                        printf("%d Readings inserted.".EOL.EOL, $counter);
                        fclose($f);
                    }
                }
                unset($balloon);
            } else {
                echo "Error: Incorrect file name".EOL;
            }
        }
    }

    closedir($handle);
    unset($db);
}

echo "End".EOL;