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
        //echo "Connessione riuscita <br>";

        $result = mysqli_query($conn , $query);

        if(!$result){
            exit("Errore : impossibile eseguire la query" . mysqli_error($conn));
        }
        //echo "query eseguita con successo";
        mysqli_close($conn);

        return $result;
    }

    $soglia_bpm = leggi_soglia();

    $warning = false;

    if(isset($_POST["value"])){

        if ( $_POST["value"] > $soglia_bpm){
            $warning = true;
        } else { $warning = false;}

        $query = "INSERT INTO `letture` (`ID`, `value`, `data`, `isWarning`) VALUES (NULL, ".$_POST["value"]." , current_timestamp() , '$warning')";
        sendQuery($query);
        $warning = false;
    }
    
    if (isset($_POST["bpmform"])){

        if ( $_POST["bpmform"] != $soglia_bpm){
            
            $file = fopen("soglia.txt" , "w") or die ("unable to open file");
            $txt = strval($_POST["bpmform"]);
            fwrite($file,$txt);
            $soglia_bpm = $_POST["bpmform"];
            echo "ho aggiornato il valore della soglia di rischio a " . $soglia_bpm;    
            fclose($file);
        } else { echo "la soglia è gia impostata a tale valore";}
    }

    if(isset($_GET["type"])){

        if ( $_GET["type"] == "get_bpm"){
            $query_bpm = "SELECT value,data,isWarning FROM `letture` WHERE 1";
            $result_get_bpm = sendQuery($query_bpm);
            echo ' 
            <table border=2>
                <tr>
                    <th>value</th>
                    <th>data</th>
                    <th>isWarning</th>
                </tr>
            ';
            while($row = mysqli_fetch_array($result_get_bpm)){
                echo '<tr>';
                echo '<td>' .$row['value']. '</td>';
                echo '<td>' .$row['data']. '</td>';
                echo '<td>' .$row['isWarning']. '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        if ( $_GET["type"] == "get_measures_with_warning" ) {
            $query = "SELECT * FROM `letture` WHERE isWarning = true";
            $result_get = sendQuery($query);
            echo mysqli_fetch_array($result_get)[1];

        }

    }

    function leggi_soglia(){
        $soglia = 0;
        $file = fopen("soglia.txt" , "r") or die ("unable to open file");
        $soglia = fread($file,filesize("soglia.txt"));
        fclose($file);
        return $soglia;
    }

?>