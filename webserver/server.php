<?php

    function sendQuery($query){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "nicore";
        $conn = mysqli_connect($servername, $username, $password,$dbname);
    
        if (!$conn) {
            die("Connessione fallita: " . mysqli_connect_error());
        } 
        echo "Connessione riuscita <br>";

        $result = mysqli_query($conn , $query);

        if(!$result){
            exit("Errore : impossibile eseguire la query" . mysqli_error($conn));
        }
        echo "query eseguita con successo";
        mysqli_close($conn);

        return $result;
    }


    if(isset($_POST["value"])){

        $query = "INSERT INTO `letture` (`ID`, `value`, `data`, `isWarning`) VALUES (NULL, ".$_POST["value"]." , current_timestamp() , '')";
        sendQuery($query);
    }

    if(isset($_GET["type"])){

        if ( $_GET["type"] == "get_measures_with_warning" ) {
            $query = "SELECT * FROM `letture` WHERE isWarning = true";
            $result_get = sendQuery($query);
            echo mysqli_fetch_array($result_get)[1];

        }

    }

?>