<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_log', 'php_errors.log');

require("config.php");

//reading the files from data folder
if ($handle = opendir('data')) {
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // checking connection
    if (mysqli_connect_errno()) {
        printf("Failed to connect: %s".EOL, mysqli_connect_error());
        exit;
    }
    
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo $entry.EOL;
            
            //parsing filename with regular expression
            if (preg_match('/(\d+)-(\w+)-(\w+)-(\w+)\.csv/', $entry, $match)) {

                $flight_number = $match[1];
                $launch_location = str_replace('_', ' ', $match[2]);
                $launch_technician = str_replace('_', ' ', $match[3]);;
                $recovered = ($match[4] == 'yes' ? 1 : 0);
                
                //data valdation
                $errors = array();
                if (!ctype_digit($flight_number)) {
                    $errors[] = 'Flight Number';
                }
                if (empty($errors)) {

                    //preparing the query
                    if ($stmt = $mysqli->prepare("REPLACE INTO balloon VALUES (?, ?, ?, ?)")) {

                        // Bind the variables to the parameter as strings. 
                        $stmt->bind_param("issi", $flight_number, $launch_location, $launch_technician, $recovered);

                        // Execute the statement.
                        $result = $stmt->execute();
                        
                        // Close the prepared statement.
                        $stmt->close();
                        
                        if ($result) {
                            printf("Balloon %d  inserted.".EOL, $flight_number);
                
                            //opening file and extracting readings
                            $f = fopen('data'.DIRECTORY_SEPARATOR.$entry, "r");
                            if ($f) {
                                $l = 0; //current file line number
                                $counter = 0; //counter of saved readings
                                while (($line = fgets($f)) !== false) {
                                    $l++;
                                    if ($l==1) continue;
                                    $vars = explode(",",trim($line));
                                    $time = $vars[0];
                                    $altitude = $vars[1];
                                    $temperature = $vars[2];
                                    $barometric_pressure = $vars[3];
                                    $humidity = $vars[4];
                                    
                                    //data valdation
                                    $errors = array();
                                    if (!strtotime($time)) $errors[] = 'Timestamp';
                                    if (!ctype_digit($altitude)) $errors[] = 'Altitude';
                                    if (!is_numeric($temperature)) $errors[] = 'Temperature';
                                    if (!ctype_digit($barometric_pressure)) $errors[] = 'Barometric Pressure';
                                    if (!is_numeric($humidity)) $errors[] = 'Humidity';
                                    if (empty($errors)) {

                                        //putting the values together
                                        $values = "0, ";
                                        $values .= $flight_number.", ";
                                        $values .= "'".$time."', ";
                                        $values .= $altitude.", ";
                                        $values .= $temperature.", ";
                                        $values .= $barometric_pressure.", ";
                                        $values .= $humidity;

                                        // executing the insertion
                                        if ($mysqli->query("INSERT into reading VALUES (".$values.")")) {
                                            $counter++;
                                        }


                                    } else {
                                        echo "Errors in the line ". $l. ", feilds: ".implode(', ', $errors).EOL;
                                    }

                                }
                                if (!feof($f)) {
                                    echo "Error: unexpected fgets() fail".EOL;
                                }
                                printf("%d Readings inserted.".EOL.EOL, $counter);
                                fclose($f);
                            }
              
                        }

                    }
                    
                } else {
                    echo "Errors in the feilds: ".implode(', ', $errors).EOL;
                }

            } else {
                echo "Error: Incorrect file name".EOL;
            }
        }
    }

    closedir($handle);
    $mysqli->close();
}

echo "End".EOL;