<?php 
    $c = curl_init('http://localhost:80/Nicore/server.php?type=get_bpm');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $bpm_table = curl_exec($c);
    if (curl_error($c))
        die(curl_error($c));
    //$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
?>

<?php 
    $c = curl_init('http://localhost:80/Nicore/server.php?type=get_measures_with_warning');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $warning_table = curl_exec($c);
    if (curl_error($c))
        die(curl_error($c));
    //$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
?>

<?php 
    $c = curl_init('http://localhost:80/Nicore/server.php?type=get_threshold');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $txt = curl_exec($c);
    if (curl_error($c))
        die(curl_error($c));
    //$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
?>

<?php 
    $c = curl_init('http://localhost:80/Nicore/server.php?type=get_last_bpm');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $last_bpm = curl_exec($c);
    if (curl_error($c))
        die(curl_error($c));
    //$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
?>

<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>


<script language="JavaScript">  //script per visualizzre il treshold attuale
    function showInput() {
        document.getElementById('display').innerHTML = 
                    document.getElementById("user_input").value;
    }
</script>

<script>

    window.onload = function () {

        var dataPoints = [];

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            theme: "light2",
            title:{
                text: "STAI VISUALIZZANDO IL CUORE DI NICOLA"
            },
            data: [{        
                type: "line",
                indexLabelFontSize: 16,
                dataPoints: dataPoints,
            }]
        });

        function addData(data) {
            var dps = data;
            for ($i in dps) {
                dataPoints.push({
                    label: dps[$i].time,
                    y: dps[$i].value,
                });
            }
            chart.render();
        }

        $.getJSON("http://localhost/Nicore/data.json", addData);

        var button = document.getElementById( "chart-button" );
        button.addEventlistener("click", refreshChart); //refresh chart

        function refreshChart(){
            chart.render();
        }
    }
</script>

<script> //per refreshare la pagina
        function refreshPage(){
            window.location.reload();
        } 
</script>

<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
        <link rel="icon" href="favicon.ico" type="image/ico">

        <title>...❤Nicore❤...</title>

    </head>

    <body>
    
    <?php        /*
        $message = "Attenzione...il tuo ultimo battito cardiaco ha superato la soglia fissata a :".' '.$txt;
        if ( $last_bpm > $txt){
            echo "<script type='text/javascript'>alert('$message');</script>";
        } */
    ?>

    <div style="text-align: center;">
        <span style="font-size: 300%;color:red; display: inline-block; ">&hearts;</span>
        <h1 class="title is-2" style="color: red; display: inline-block; font-family: Courier New, Courier, monospace">NICORE</h1>
        <span style="font-size: 300%;color:red; display: inline-block;">&hearts;</span>
    </div>

    <div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto; align: center;"></div>

    <div style="text-align: center; margin-top: 25px">
            <button id="chart-button" type="button" onClick="refreshPage()" class="button" style="height: 60px; width 200px; background-color: red; color: white  ">
            &#8635 chart
            </button>
    </div>

        

    <div>
        <form style="vertical-align: top; float: left" id="bpmform" action="http://localhost:80/Nicore/server.php" method="POST" target="ifrm1" onsumbit="target_popup(this)">
            <label style="margin-left: 20px">Inserisci soglia rischio bpm:</label>
            <input name="bpmform" type="number" id="user_input">
            <input type="submit" onclick="showInput()">
            <span style="font-size: 25px;color:red; display: inline-block; ">&hearts;</span><br></br>
            <p style="margin-left: 20px">Valore soglia aggiornato a : <span id='display'><?php echo $txt; ?></span> bpm</p> 
        </form>

        <button type="button" onClick="refreshPage()" class="button-solid" style="background-color: transparent; border: none; margin-left: 600px">
            <p style="font-size: 20px">Click on the heart to &#8635</p><span style="font-size: 100px;color: red; "><span>&#8594;</span>&hearts;<span>&#8592;</span></span><br></br>
        </button>

        <iframe id="ifrm1" name="ifrm1" style="display:none"></iframe>
    </div>


    <div style="align: center; display: inline-block; margin-left: 50px";>
        <h3>Storico battiti cardiaci nelle ultime 24h</h3>
            <?php
                echo $bpm_table;
            ?>
    </div>

    <div style="align: center; display: inline-block; margin-left: 200px";>
        <h3>Battiti misurati che hanno superato la soglia di rischio</h3>
            <?php
                echo $warning_table;
            ?>
    </div>

    </body>
</html>