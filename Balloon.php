<?php

class Balloon {
    public $flight_number;
    public $launch_location;
    public $launch_technician;
    public $recovered;
    
    public function __construct($data) {
        $this->flight_number = $data[1];
        $this->launch_location = str_replace('_', ' ', $data[2]);
        $this->launch_technician = str_replace('_', ' ', $data[3]);;
        $this->recovered = ($data[4] == 'yes' ? 1 : 0);
    }
    
    public function save($link) {
        
        //data valdation
        $errors = array();
        if (!ctype_digit($this->flight_number)) {
            $errors[] = 'Flight Number';
        }
        if (empty($errors)) {
            
            //putting the values  together
            $values = $this->flight_number.",";
            $values .= "'".mysqli_real_escape_string($link, $this->launch_location)."',";
            $values .= "'".mysqli_real_escape_string($link, $this->launch_technician)."',";
            $values .= $this->recovered;
            
            // executing the insertion
            if (mysqli_query($link, "REPLACE into balloon VALUES (".$values.")")) {
                printf("Balloon %d  inserted.".EOL, $this->flight_number);
                return true;
            }
            
        } else {
            echo "Errors in the feilds: ".implode(', ', $errors).EOL;
            return false;
        }
    }
    
}