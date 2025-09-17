// Pega o canvas
const ctx = document.getElementById('consumoChart');

// Cria o gráfico
new Chart(ctx, {
  type: 'line', // pode ser 'bar', 'pie', etc.
  data: {
    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'], // meses
    datasets: [{
      label: 'Consumo (kWh)',
      data: [320, 310, 280, 290, 270, 250], // valores simulados
      borderColor: '#ff9800',
      backgroundColor: 'rgba(255,152,0,0.2)',
      borderWidth: 2,
      fill: true,
      tension: 0.4 // curva suave na linha
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        labels: {
          color: '#fff',
          font: { size: 14 }
        }
      }
    },
    scales: {
      x: {
        ticks: { color: '#fff' },
        grid: { color: 'rgba(255,255,255,0.1)' }
      },
      y: {
        ticks: { color: '#fff' },
        grid: { color: 'rgba(255,255,255,0.1)' }
      }
    }
  }
});
