import Chart from 'chart.js/auto';

const COLORS = {
  primary: '#0B3D91',
  secondary: '#1E6F5C',
  gold: '#D4AF37',
  red: '#DC2626',
  slate: '#64748B',
  amber: '#D97706',
};

function chartTextColor() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? 'rgba(226, 232, 240, 0.72)'
    : 'rgba(7, 35, 79, 0.62)';
}

function chartGridColor() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? 'rgba(255, 255, 255, 0.08)'
    : 'rgba(11, 61, 145, 0.10)';
}

function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          color: chartTextColor(),
          font: {
            family: 'Inter',
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
          backgroundColor: 'rgba(11, 61, 145, 0.12)',
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: COLORS.gold,
          pointBorderColor: COLORS.gold,
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
        cutout: '64%',
      },
    });
  }
}

document.addEventListener('DOMContentLoaded', initDashboardCharts);
