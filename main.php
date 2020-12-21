<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Google charts</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["calendar"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({type: 'date', id: 'Date'});
            dataTable.addColumn({type: 'number', id: 'Won/Loss'});
            <?php
            $connection = mysqli_connect(
                "localhost",
                "root",
                "",
                "firm");
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                exit;
            }
            if (!$connection->set_charset("utf8")) {
                printf("Error loading character set utf8: %s\n", $connection->error);
            }

            $sql = "SELECT DISTINCT `reports`.`date` AS Date_u,
(SELECT SUM(`reports`.`external_project`*`cost_of_work`.`salary`)-SUM(`reports`.`internal_project`*`cost_of_work`.`salary`)
 FROM `reports`
INNER JOIN `cost_of_work` on `reports`.`id`=`cost_of_work`.`id`
WHERE `reports`.`date`=Date_u)  AS total
FROM `reports`";
            $result = mysqli_query($connection, $sql);

            while ($data = mysqli_fetch_assoc($result)) {
                $date = explode("-", $data['Date_u']);
                $year = (int)$date[0];
                $month = (int)$date[1] - 1;
                $day = (int)$date[2];
                echo "dataTable.addRows([[new Date(" . "$year, $month, $day)," . $data['total'] . "]]);\n";
            }
            mysqli_close($connection);
            ?>
            var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));

            var options = {
                title: "Облік відпрацьованих годин працівниками фірми",
                height: 350,
            };
            chart.draw(dataTable, options);
            google.visualization.events.addListener(chart, 'select', function () {
                var row = chart.getSelection()[0].row;
                if (row != undefined) {
                    var id_date = dataTable.getValue(row, 0);
                    showHint(id_date);
                }
            });
        }
    </script>
</head>
<body>

<div id="calendar_basic" style="width: 1000px; height: 350px;"></div>
<div id="information_about_day"></div>
</body>
</html>
