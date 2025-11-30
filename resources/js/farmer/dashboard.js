// Farmer Dashboard Scripts
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

   // --- Monthly Sales Chart (Bar) ---
   var salesOptions = {
      series: [{
         name: 'Ventas',
         data: [2300, 3100, 2800, 4200, 3800, 4800, 3500, 4100, 3200, 4500, 5100, 4800]
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
            columnWidth: '45%',
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
         categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
         labels: { style: { colors: colors.textLight } },
         axisBorder: { show: false },
         axisTicks: { show: false }
      },
      yaxis: {
         title: { text: 'S/.', style: { color: colors.textLight } },
         labels: { style: { colors: colors.textLight } }
      },
      fill: { opacity: 1 },
      colors: [colors.primary],
      tooltip: {
         y: {
            formatter: function (val) {
               return "S/. " + val
            }
         }
      },
      grid: {
         borderColor: '#e8f0db',
         strokeDashArray: 4,
      }
   };

   if (document.querySelector("#monthlySalesChart")) {
      var salesChart = new ApexCharts(document.querySelector("#monthlySalesChart"), salesOptions);
      salesChart.render();
   }

   // --- Monthly Target Chart (Donut) ---
   var targetOptions = {
      series: [75.55, 24.45],
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
                     fontSize: '14px',
                     fontWeight: '600',
                     color: colors.textLight,
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

   if (document.querySelector("#monthlyTargetChart")) {
      var targetChart = new ApexCharts(document.querySelector("#monthlyTargetChart"), targetOptions);
      targetChart.render();
   }
});
