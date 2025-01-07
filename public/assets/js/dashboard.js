

$(function () {
    // =====================================
    // Profit (Production and Delivery)
    // =====================================
    var chart = {
      series: [
        { name: "Earnings this month:", data: weeklyDeliveryData },
      ],
  
      chart: {
        type: "bar",
        height: 345,
        offsetX: -15,
        toolbar: { show: true },
        foreColor: "#adb0bb",
        fontFamily: 'inherit',
        sparkline: { enabled: false },
      },
  
  
      colors: ["#5D87FF", "#49BEFF"],
  
  
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: "35%",
          borderRadius: [6],
          borderRadiusApplication: 'end',
          borderRadiusWhenStacked: 'all'
        },
      },
      markers: { size: 0 },
  
      dataLabels: {
        enabled: false,
      },
  
  
      legend: {
        show: false,
      },
  
  
      grid: {
        borderColor: "rgba(0,0,0,0.1)",
        strokeDashArray: 3,
        xaxis: {
          lines: {
            show: false,
          },
        },
      },
  
      xaxis: {
        type: "category",
        categories: ["ke-1", "ke-2", "ke-3", "ke-4"],
        labels: {
          style: { cssClass: "grey--text lighten-2--text fill-color" },
        },
      },
  
  
      yaxis: {
        show: true,
        min: 0,
        max: 500,
        tickAmount: 10,
        labels: {
          style: {
            cssClass: "grey--text lighten-2--text fill-color",
          },
        },
      },
      stroke: {
        show: true,
        width: 3,
        lineCap: "butt",
        colors: ["transparent"],
      },
  
  
      tooltip: { theme: "light" },
  
      responsive: [
        {
          breakpoint: 600,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 3,
              }
            },
          }
        }
      ]
  
  
    };
  
    var chart = new ApexCharts(document.querySelector("#chart"), chart);
    chart.render();

    // =====================================
    // Breakup (Production vs Delivery Count)
    // =====================================
    var breakup = {
        color: "#adb5bd",
        series: [produksiThisMonth, produksiLastMonth], // Dynamic production and delivery count
        labels: ["This Month", "Last Month"],
        chart: {
            width: 190,
            type: "donut",
            fontFamily: "Plus Jakarta Sans', sans-serif",
            foreColor: "#adb0bb",
        },
        plotOptions: {
            pie: {
                startAngle: 0,
                endAngle: 360,
                donut: {
                    size: '75%',
                },
            },
        },
        stroke: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        colors: ["#5D87FF", "#ecf2ff", "#F9F9FD"],
        responsive: [{
            breakpoint: 991,
            options: {
                chart: {
                    width: 150,
                },
            },
        }, ],
        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#breakup"), breakup);
    chart.render();

    // =====================================
    // Earning (Example)
    // =====================================
    var earning = {
        chart: {
            id: "sparkline3",
            type: "area",
            height: 60,
            sparkline: {
                enabled: true,
            },
            group: "sparklines",
            fontFamily: "Plus Jakarta Sans', sans-serif",
            foreColor: "#adb0bb",
        },
        series: [{
            name: "Delivery",
            color: "#49BEFF",
            data: deliveryCounts, // Menggunakan data yang telah dihitung
        }, ],
        stroke: {
            curve: "smooth",
            width: 2,
        },
        fill: {
            colors: ["#f3feff"],
            type: "solid",
            opacity: 0.05,
        },
        markers: {
            size: 0,
        },
        tooltip: {
            theme: "dark",
            fixed: {
                enabled: true,
                position: "right",
            },
            x: {
                show: false,
            },
        },
    };

    new ApexCharts(document.querySelector("#earning"), earning).render();
});
