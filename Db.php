<?php

class DB {
    public $link;
    
    public function __construct() {
        //connecting to mysql database
        $db_link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // checking connection
        if (mysqli_connect_errno()) {
            printf("Failed to connect: %s".EOL, mysqli_connect_error());
            exit();
        }

        $this->link = $db_link;        
    }
    
    public function __destruct() {
        mysqli_close($this->link);
    }
}