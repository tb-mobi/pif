<div id="pie-chart-div" class="charts"></div>
<script type="text/javascript">
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1.0', {'packages':['corechart']});
    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);
    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {
        var data = google.visualization.arrayToDataTable(
            [
                ['2015-07-20', 1000, 1001, 1010, 1012,],
                ['2015-07-21', 1001, 1002, 1011, 1013,],
                ['2015-07-22', 1002, 1003, 1012, 1014,],
                ['2015-07-23', 1003, 1004, 1013, 1015,],
                ['2015-07-24', 1004, 1005, 1014, 1016,],
                ['2015-07-25', 1003, 1005, 1015, 1017,],
                ['2015-07-26', 1005, 1005, 1016, 1018,],
                ['2015-07-27', 1006, 1005, 1017, 1019,],
                ['2015-07-28', 1007, 1006, 1018, 1020,],
                ['2015-07-29', 1008, 1007, 1019, 1021,],
                ['2015-07-30', 1007, 1008, 1019, 1022,],
                ['2015-07-31', 1009, 1009, 1018, 1023,],
                ['2015-08-01', 1011, 1015, 1020, 1024,],
                ['2015-08-02', 1010, 1015, 1021, 1025,],
                ['2015-08-03', 1014, 1015, 1023, 1026,]
            ]
            , true // Treat first row as data as well.
        );
        // Set chart options
        var options = {
            title:'Pie currency',
            legend:'none',
            width:720,
            // Allow multiple
            // simultaneous selections.
            selectionMode: 'multiple',
            // Trigger tooltips
            // on selections.
            tooltip: {trigger: 'selection'},
            // Group selections
            // by x-value.
            aggregationTarget: 'category',
            bar: { groupWidth: '40%' }, // Remove space between bars.
            candlestick: {
                fallingColor: { strokeWidth: 0, fill: '#a52714' }, // red
                risingColor: { strokeWidth: 0, fill: '#0f9d58' }   // green
            }
        };
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.CandlestickChart(document.getElementById('pie-chart-div'));
        chart.draw(data, options);
    }
</script>
