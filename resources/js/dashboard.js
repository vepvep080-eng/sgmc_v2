import Chart from 'chart.js/auto';

const COLORS = {
  primary: '#0D4A36',
  secondary: '#A67A1E',
  gold: '#C99738',
  red: '#BA2525',
  slate: '#707974',
  amber: '#D97706',
};

function chartTextColor() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? 'rgba(220, 247, 234, 0.72)'
    : 'rgba(8, 32, 24, 0.62)';
}

function chartGridColor() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? 'rgba(255, 255, 255, 0.08)'
    : 'rgba(13, 74, 54, 0.10)';
}

function chartValueColor() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? 'rgba(220, 247, 234, 0.9)'
    : 'rgba(8, 32, 24, 0.85)';
}

// Draws the numeric value above each point/bar (line & bar charts only).
const valueLabelsPlugin = {
  id: 'sgmcValueLabels',
  afterDatasetsDraw(chart) {
    const type = chart.config ? chart.config.type : '';
    if (type !== 'line' && type !== 'bar') return;

    const data = chart.data.datasets[0].data;
    if (!data || !data.length) return;

    const area = chart.chartArea;
    const step = (area.right - area.left) / data.length;
    const ctx = chart.ctx;

    ctx.save();
    ctx.font = "600 10.5px 'IBM Plex Sans', 'Segoe UI', sans-serif";
    ctx.textAlign = 'center';
    ctx.textBaseline = 'bottom';
    ctx.fillStyle = chartValueColor();

    data.forEach((value, i) => {
      if (value === null || value === undefined) return;
      const x = area.left + step * (i + 0.5);
      const y = chart.scales.y.getPixelForValue(value) - (type === 'bar' ? 5 : 9);
      ctx.fillText(String(value), x, y);
    });

    ctx.restore();
  },
};

Chart.register(valueLabelsPlugin);

function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          color: chartTextColor(),
          font: {
            family: 'IBM Plex Sans',
            size: 12,
          },
          usePointStyle: true,
          padding: 16,
        },
      },
    },
  };
}

function initDashboardCharts() {
  const container = document.querySelector('[data-dashboard-charts]');

  if (!container || !container.dataset.chartData) {
    return;
  }

  const data = JSON.parse(container.dataset.chartData);
  const commonOptions = baseOptions();

  const monthlyCanvas = document.getElementById('courriers-par-mois-chart');

  if (monthlyCanvas) {
    new Chart(monthlyCanvas, {
      type: 'line',
      data: {
        labels: data.par_mois.labels,
        datasets: [{
          label: 'Courriers reçus',
          data: data.par_mois.data,
          borderColor: COLORS.primary,
          backgroundColor: 'rgba(13, 74, 54, 0.12)',
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: data.par_mois.data.map(
            (_, i) => (i === data.par_mois.data.length - 1) ? COLORS.gold : COLORS.primary
          ),
          pointBorderColor: data.par_mois.data.map(
            (_, i) => (i === data.par_mois.data.length - 1) ? COLORS.gold : COLORS.primary
          ),
        }],
      },
      options: {
        ...commonOptions,
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: chartTextColor(),
            },
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: chartTextColor(),
              precision: 0,
            },
            grid: {
              color: chartGridColor(),
            },
          },
        },
      },
    });
  }

  const serviceCanvas = document.getElementById('courriers-par-service-chart');

  if (serviceCanvas) {
    new Chart(serviceCanvas, {
      type: 'bar',
      data: {
        labels: data.par_service.labels,
        datasets: [{
          label: 'Courriers',
          data: data.par_service.data,
          backgroundColor: [
            COLORS.primary,
            COLORS.secondary,
            COLORS.gold,
            COLORS.amber,
            COLORS.slate,
            COLORS.red,
          ],
          borderRadius: 8,
          borderSkipped: false,
        }],
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: chartTextColor(),
            },
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: chartTextColor(),
              precision: 0,
            },
            grid: {
              color: chartGridColor(),
            },
          },
        },
      },
    });
  }

  const statusCanvas = document.getElementById('repartition-statut-chart');

  if (statusCanvas) {
    new Chart(statusCanvas, {
      type: 'doughnut',
      data: {
        labels: data.par_statut.labels,
        datasets: [{
          data: data.par_statut.data,
          backgroundColor: [
            COLORS.primary,
            COLORS.gold,
            COLORS.secondary,
            '#16A34A',
            COLORS.slate,
          ],
          borderColor: 'transparent',
          hoverOffset: 8,
        }],
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          legend: { display: false },
        },
        cutout: '64%',
      },
    });
  }

  const priorityCanvas = document.getElementById('repartition-priorite-chart');

  if (priorityCanvas) {
    new Chart(priorityCanvas, {
      type: 'doughnut',
      data: {
        labels: data.par_priorite.labels,
        datasets: [{
          data: data.par_priorite.data,
          backgroundColor: [
            COLORS.slate,
            COLORS.primary,
            COLORS.amber,
            COLORS.red,
          ],
          borderColor: 'transparent',
          hoverOffset: 8,
        }],
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          legend: { display: false },
        },
        cutout: '64%',
      },
    });
  }
}

document.addEventListener('DOMContentLoaded', initDashboardCharts);
