<?php 
    $c = curl_init('http://localhost:80/Nicore/server.php?type=get_bpm');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $bpm_table = curl_exec($c);
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

<html>

    <head>
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
                        { y: 414},
                        { y: 520, indexLabel: "\u2191 highest",markerColor: "red", markerType: "triangle" },
                        { y: 460 },
                        { y: 450 },
                        { y: 500 },
                        { y: 480 },
                        { y: 480 },
                        { y: 410 , indexLabel: "\u2193 lowest",markerColor: "DarkSlateGrey", markerType: "cross" },
                        { y: 500 },
                        { y: 480 },
                        { y: 510 }
                    ]
                }]
            });
            chart.render();
            
            }
            </script>
    </head>

    <body>

    <h1>NICORE</h1>

    <p>Benvenuto su nicore.</p>

    <div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <div>
        <form id="bpmform" action="http://localhost:80/Nicore/server.php" method="POST" target="_blank""">
            <label>Inserisci soglia rischio bpm:</label>
            <input name="bpmform" type="number">
            <input type="submit">
        </form>
    </div>

    <div>
        <h3>Storico battiti cardiaci</h3>
            <?php
                echo $bpm_table;
            ?>
    </div>

    </body>
</html>