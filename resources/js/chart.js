import Chart from 'chart.js/auto';

window.initBalanceChart = function(data) {
    console.log('Initializing chart with data:', data);
    
    const ctx = document.getElementById('balanceChart');
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    const style = getComputedStyle(document.documentElement);
    
    const chartColors = [
        style.getPropertyValue('--chart-color-1'),
        style.getPropertyValue('--chart-color-2'),
        style.getPropertyValue('--chart-color-3'),
        style.getPropertyValue('--chart-color-4'),
        style.getPropertyValue('--chart-color-5'),
        style.getPropertyValue('--chart-color-6'),
        style.getPropertyValue('--chart-color-7'),
        style.getPropertyValue('--chart-color-8'),
    ];

    console.log('Using colors:', chartColors);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: chartColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const percentage = ((value / context.dataset.data.reduce((a, b) => a + b)) * 100).toFixed(1);
                            return `${label}: €${value.toFixed(2)} (${percentage}%)`;
                        }
                    },
                    titleFont: {
                        family: "'Inter', sans-serif",
                        size: 12
                    },
                    bodyFont: {
                        family: "'Inter', sans-serif",
                        size: 12
                    }
                }
            }
        }
    });
}; 