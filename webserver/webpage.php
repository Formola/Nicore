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

<script>
    var target_popup = function(form){
        window.open('',
                    'UniqueWindowName',
                    'width=400 , heigh;400, resizeable,scrollbars');
        form.target = 'UniqueWindowName';
    }
</script>

<script language="JavaScript">
    function showInput() {
        document.getElementById('display').innerHTML = 
                    document.getElementById("user_input").value;
    }
  </script>

<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.2/css/bulma.css" rel="stylesheet"/>
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
        <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
        <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
        <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script></script>

        <title>Nicore Webpage</title>

        <script>
            window.onload = function () {
            
            var dataPoints = [];

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "STAI VISUALIZZANDO IL TUO CUORE NICOLA"
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
        }
        </script>
    </head>

    <body>

    <div style="text-align: center;">
    
        <span style="font-size: 300%;color:red; display: inline-block; ">&hearts;</span>
        <h1 class="title is-2" style="color: red; display: inline-block; font-family: Courier New, Courier, monospace">NICORE</h1>
        <span style="font-size: 300%;color:red; display: inline-block;">&hearts;</span>
    </div>

    <div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>

    <div style="margin-top : 50px">
        <form id="bpmform" action="http://localhost:80/Nicore/server.php" method="POST" target="ifrm1" onsumbit="target_popup(this)">
            <label style="margin-left: 20px">Inserisci soglia rischio bpm:</label>
            <input name="bpmform" type="number" id="user_input">
            <input type="submit" onclick="showInput()">
            <span style="font-size: 25px;color:red; display: inline-block; ">&hearts;</span><br></br>
            <p style="margin-left: 20px">Valore soglia aggiornato a : <span id='display'><?php echo $txt; ?></span> bpm</p>
            
        </form>
        <iframe id="ifrm1" name="ifrm1" style="display:none"></iframe>

    </div>

    <br></br>
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