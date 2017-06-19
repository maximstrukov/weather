<?php

class Reading {
    public $id;
    public $flight;
    public $time;
    public $altitude;
    public $temperature;
    public $barometric_pressure;
    public $humidity;
    
    public function __construct($data) {
        $this->flight = $data[0];
        $this->time = $data[1];
        $this->altitude = $data[2];
        $this->temperature = $data[3];
        $this->barometric_pressure = $data[4];
        $this->humidity = $data[5];
    }
    
    public function save($link, $line) {
        
        //data valdation
        $errors = array();
        if (!strtotime($this->time)) $errors[] = 'Timestamp';
        if (!ctype_digit($this->altitude)) $errors[] = 'Altitude';
        if (!is_numeric($this->temperature)) $errors[] = 'Temperature';
        if (!ctype_digit($this->barometric_pressure)) $errors[] = 'Barometric Pressure';
        if (!is_numeric($this->humidity)) $errors[] = 'Humidity';
        if (empty($errors)) {
            
            //putting the values together
            $values = "0, ";
            $values .= $this->flight.", ";
            $values .= "'".$this->time."', ";
            $values .= $this->altitude.", ";
            $values .= $this->temperature.", ";
            $values .= $this->barometric_pressure.", ";
            $values .= $this->humidity;

            // executing the insertion
            if (mysqli_query($link, "INSERT into reading VALUES (".$values.")")) {
                return true;
            }
            
        } else {
            echo "Errors in the line ". $line. ", feilds: ".implode(', ', $errors).EOL;
            return false;
        }
    }
}