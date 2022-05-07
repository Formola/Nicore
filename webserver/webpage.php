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

<script>  //test per provare a rimanere sulla stessa pagina
    $(function(){
        $('#bpmform').submit(function(event) {
            event.preventDefault();
            $(this).submit();
        });
    });
</script>

<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.2/css/bulma.css" rel="stylesheet"/>
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
        <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

        <title>Nicore Webpage</title>
        <script>
            window.onload = function () {
            
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Simple Line Chart"
                },
                data: [{        
                    type: "line",
                      indexLabelFontSize: 16,
                    dataPoints: [
                        { y: 450 },
                    ]
                }]
            });
            chart.render();
            
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
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <div style="margin-top : 50px">
        <form id="bpmform" action="http://localhost:80/Nicore/server.php" method="POST" target="_blank""">
            <label>Inserisci soglia rischio bpm:</label>
            <input name="bpmform" type="number">
            <input type="submit">
            <span style="font-size: 25  px;color:red; display: inline-block; ">&hearts;</span>
        </form>

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