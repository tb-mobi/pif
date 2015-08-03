<div id="history-chart-div" class="charts"></div>
<script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['Продукты питания', 13600],
          ['Сотовая связь', 1200],
          ['Прочее', 8600],
          ['Строй материалы', 4750],
          ['Автозаправка', 4670]
        ]);

        // Set chart options
        var options = {
            title:'Расходы за период',
            pieHole: 0.4,
            pieSliceText:'label',
            width:720,
            height:480
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('history-chart-div'));
        chart.draw(data, options);
      }
    </script>
