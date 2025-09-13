// chart 1

if (document.querySelector("#chart-bars")) {
  
  var ctx = document.getElementById("chart-bars").getContext("2d");
  
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [
        {
          label: "Sales",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#fff",
          data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
          maxBarThickness: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      interaction: {
        intersect: false,
        mode: "index",
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
          },
          ticks: {
            suggestedMin: 0,
            suggestedMax: 600,
            beginAtZero: true,
            padding: 15,
            font: {
              size: 14,
              family: "Open Sans",
              style: "normal",
              lineHeight: 2,
            },
            color: "#fff",
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
          },
          ticks: {
            display: false,
          },
        },
      },
    },
  });
}

// chart 2

if(document.querySelector("#chart-line")){
const chartElement = document.querySelector("#chart-line");

  console.info('[Dashboard] Initializing chart-line');

  // 读取并解析数据
  const labelsRaw = chartElement.dataset.labels;
  const dataRaw = chartElement.dataset.data;
  const labelName = chartElement.dataset.labelName;

  console.debug('[Dashboard] chart-line raw dataset', { labelsRaw, dataRaw, labelName });

  let labels = [];
  let data = [];
  try {
    labels = JSON.parse(labelsRaw || '[]');
  } catch (e) {
    console.error('[Dashboard] Failed to parse labels JSON', e, labelsRaw);
    labels = [];
  }
  try {
    data = JSON.parse(dataRaw || '[]');
  } catch (e) {
    console.error('[Dashboard] Failed to parse data JSON', e, dataRaw);
    data = [];
  }

  if (!Array.isArray(labels) || !Array.isArray(data)) {
    console.warn('[Dashboard] labels/data are not arrays', { labels, data });
  }
  if (labels.length === 0 || data.length === 0) {
    console.warn('[Dashboard] Empty chart series', { labelsLength: labels.length, dataLength: data.length });
    // Update subtitle message to indicate no data
    try {
      var subtitleEl = document.getElementById('sales-overview-subtitle');
      if (subtitleEl) {
        subtitleEl.innerHTML = '<span class="font-semibold">No sales data</span> for the selected period';
      }
    } catch (e) {
      console.debug('[Dashboard] Failed to update subtitle', e);
    }
  }
  if (labels.length !== data.length) {
    console.warn('[Dashboard] Label/Data length mismatch', { labelsLength: labels.length, dataLength: data.length });
  }

  var ctx1 = document.getElementById("chart-line").getContext("2d");

  var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

  gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
  gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
  gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
  new Chart(ctx1, {
    type: "line",
    data: {
      labels: labels,
      datasets: [{
        label: labelName,
        tension: 0.4,
        borderWidth: 0,
        pointRadius: 0,
        borderColor: "#5e72e4",
        backgroundColor: gradientStroke1,
        borderWidth: 3,
        fill: true,
        data: data,
        maxBarThickness: 6

      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            padding: 10,
            color: '#fbfbfb',
            font: {
              size: 11,
              family: "Open Sans",
              style: 'normal',
              lineHeight: 2
            },
          }
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            color: '#ccc',
            padding: 20,
            font: {
              size: 11,
              family: "Open Sans",
              style: 'normal',
              lineHeight: 2
            },
          }
        },
      },
    },
  });
  // When there is no data, draw a placeholder message on the canvas
  try {
    if (!data || data.length === 0) {
      const { width, height } = ctx1.canvas;
      ctx1.save();
      ctx1.clearRect(0, 0, width, height);
      ctx1.fillStyle = '#9aa0a6';
      ctx1.font = '14px Open Sans, sans-serif';
      ctx1.textAlign = 'center';
      ctx1.textBaseline = 'middle';
      ctx1.fillText('No sales data to display', width / 2, height / 2);
      ctx1.restore();
    }
  } catch (e) {
    console.debug('[Dashboard] Failed to draw empty placeholder', e);
  }
  console.info('[Dashboard] chart-line created', { points: data.length });
}