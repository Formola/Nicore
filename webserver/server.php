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

        $query = "INSERT INTO `letture` (`ID`, `value`, `data`, `isWarning` , `threshold`) VALUES (NULL, ".$_POST["value"]." , current_timestamp() , '$warning' , $soglia_bpm)";
        sendQuery($query);

        $bpm_json = file_get_contents('data.json');
        $decoded_json = json_decode($bpm_json,true);

        $get_id = "SELECT ID,data FROM `letture` WHERE data ORDER BY ID DESC LIMIT 1;";
        $result_get_id = sendQuery($get_id);

        while($row = mysqli_fetch_array($result_get_id)){
            array_push($decoded_json,(object)[
                
                'time' => $row['data'],
                'value' => (int)$_POST["value"]
            
            ]);
        }

        $f = fopen("data.json", "w");
        fwrite($f, json_encode($decoded_json, JSON_PRETTY_PRINT));
        fclose($f);
        //print_r($decoded_json);

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
        } else { echo "la soglia ?? gia impostata a tale valore";}
    }

    if(isset($_GET["type"])){

        if ( $_GET["type"] == "get_threshold"){
            echo $soglia_bpm;
        }


        if ( $_GET["type"] == "get_bpm"){
            $query_bpm = "SELECT value,data,isWarning,threshold FROM `letture` WHERE data >= NOW() - INTERVAL 1 DAY ORDER BY data DESC";  //ultime 24h
            $result_get_bpm = sendQuery($query_bpm);
            echo ' 
            <table class="table is-bordered" border=2>
                <tr>
                    <th>value <span style="font-size: 15px;color:red; display: inline-block; ">&hearts;</span></th>
                    <th>data</th>
                    <th>isWarning</th>
                    <th>threshold <span style="font-size: 20px;color:black; display: inline-block; ">&#9760;</span>    </th>
                </tr>
            ';
            while($row = mysqli_fetch_array($result_get_bpm)){
                echo '<tr>';
                echo '<td>' .$row['value']. '</td>';
                echo '<td>' .$row['data']. '</td>';
                echo '<td>' .$row['isWarning']. '</td>';
                echo '<td>' .$row['threshold']. '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        if ( $_GET["type"] == "get_measures_with_warning" ) {
            $warning_query = "SELECT * FROM `letture` WHERE isWarning = true AND data ORDER BY data DESC";
            $warning_result = sendQuery($warning_query);
            echo ' 
            <table class="table is-bordered" border=2>
                <tr>
                    <th>value <span style="font-size: 15px;color:red; display: inline-block; ">&hearts;</span></th>
                    <th>data</th>
                    <th>isWarning</th>
                    <th>threshold <span style="font-size: 20px;color:black; display: inline-block; ">&#9760;</span></th>
                </tr>
            ';
            while($row = mysqli_fetch_array($warning_result)){
                echo '<tr>';
                echo '<td>' .$row['value']. '</td>';
                echo '<td>' .$row['data']. '</td>';
                echo '<td>' .$row['isWarning']. '</td>';
                echo '<td>' .$row['threshold']. '</td>';
                echo '</tr>';
            }
            echo '</table>'; 
        }

        if ( $_GET["type"] == "get_last_bpm"){
            $query_last_bpm = "SELECT value FROM `letture` ORDER BY ID DESC LIMIT 1;";
            $result_query_last_bpm = sendQuery($query_last_bpm);
            while($row = mysqli_fetch_array($result_query_last_bpm)){
                echo $row['value'];
            }
            
        }

    }

    function leggi_soglia(){
        $soglia = 0;
        $file = fopen("soglia.txt" , "r") or die ("unable to open file");
        if(filesize("soglia.txt")>0){
            $soglia = fread($file,filesize("soglia.txt"));
        }
        fclose($file);
        return $soglia;
    }

?>