// Admin Dashboard Scripts
document.addEventListener('DOMContentLoaded', function () {
   // Theme Colors
   const colors = {
      primary: '#7AA537',
      secondary: '#d8efc0',
      text: '#2a3e1f',
      textLight: '#5a6f4a',
      bg: '#f0f4e8',
      card: '#ffffff'
   };

   // --- Weekly Sales Chart (Bar) ---
   var salesOptions = {
      series: [{
         name: 'Ventas',
         data: [44, 55, 57, 56, 61, 58, 63]
      }, {
         name: 'Ingresos',
         data: [76, 85, 101, 98, 87, 105, 91]
      }],
      chart: {
         type: 'bar',
         height: 350,
         toolbar: { show: false },
         fontFamily: 'Inter, sans-serif'
      },
      plotOptions: {
         bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded',
            borderRadius: 4
         },
      },
      dataLabels: { enabled: false },
      stroke: {
         show: true,
         width: 2,
         colors: ['transparent']
      },
      xaxis: {
         categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
         labels: { style: { colors: colors.textLight } }
      },
      yaxis: {
         title: { text: 'S/. (miles)', style: { color: colors.textLight } },
         labels: { style: { colors: colors.textLight } }
      },
      fill: { opacity: 1 },
      colors: [colors.primary, colors.secondary],
      tooltip: {
         y: {
            formatter: function (val) {
               return "S/. " + val + " miles"
            }
         }
      },
      grid: { borderColor: '#e8f0db' }
   };
   var salesChart = new ApexCharts(document.querySelector("#weeklySalesChart"), salesOptions);
   salesChart.render();

   // --- Weekly Target Chart (Donut) ---
   var targetOptions = {
      series: [75, 25],
      chart: {
         type: 'donut',
         height: 350,
         fontFamily: 'Inter, sans-serif'
      },
      labels: ['Completado', 'Restante'],
      colors: [colors.primary, '#f0ecd6'],
      plotOptions: {
         pie: {
            donut: {
               size: '75%',
               labels: {
                  show: true,
                  name: { show: false },
                  value: {
                     show: true,
                     fontSize: '28px',
                     fontWeight: 'bold',
                     color: colors.text,
                     formatter: function (val) {
                        return val + "%";
                     }
                  },
                  total: {
                     show: true,
                     showAlways: true,
                     label: 'Total',
                     fontSize: '22px',
                     fontWeight: 'bold',
                     color: colors.text,
                     formatter: function (w) {
                        return '75.55%';
                     }
                  }
               }
            }
         }
      },
      dataLabels: { enabled: false },
      legend: { show: false },
      stroke: { show: false }
   };
   var targetChart = new ApexCharts(document.querySelector("#weeklyTargetChart"), targetOptions);
   targetChart.render();

   // --- Statistics Chart (Area/Line) ---
   var statsOptions = {
      series: [{
         name: 'Ingresos',
         data: [31, 40, 28, 51, 42, 109, 100]
      }, {
         name: 'Gastos',
         data: [11, 32, 45, 32, 34, 52, 41]
      }],
      chart: {
         height: 350,
         type: 'area',
         toolbar: { show: false },
         fontFamily: 'Inter, sans-serif'
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth' },
      xaxis: {
         categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
         labels: { style: { colors: colors.textLight } }
      },
      yaxis: {
         labels: { style: { colors: colors.textLight } }
      },
      colors: [colors.primary, '#a8d4e8'],
      tooltip: {
         x: { format: 'dd/MM/yy HH:mm' },
      },
      grid: { borderColor: '#e8f0db' }
   };
   var statsChart = new ApexCharts(document.querySelector("#statisticsChart"), statsOptions);
   statsChart.render();
});
