window.initLiveTrafficChart = function (elementId, interfaceName) {
    const ctx = document.getElementById(elementId).getContext('2d');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Upload (Mbps)',
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.3)',
                    data: [],
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: 'Download (Mbps)',
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.3)',
                    data: [],
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: "Speed (Mbps)" },
                }
            }
        }
    });

    setInterval(async () => {
        try {
            const url = `https://swiftel.co.ke/api/live-traffic/${interfaceName}`;
            const response = await fetch(url);
            const data = await response.json();

            const timeLabel = new Date().toLocaleTimeString();

            const uploadMbps = (data.tx_bps / 1_000_000).toFixed(3);
            const downloadMbps = (data.rx_bps / 1_000_000).toFixed(3);

            chart.data.labels.push(timeLabel);
            chart.data.datasets[0].data.push(uploadMbps);
            chart.data.datasets[1].data.push(downloadMbps);

            if (chart.data.labels.length > 20) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
                chart.data.datasets[1].data.shift();
            }

            chart.update();
        } catch (err) {
            console.log("Traffic API error:", err);
        }
    }, 2000);
};
