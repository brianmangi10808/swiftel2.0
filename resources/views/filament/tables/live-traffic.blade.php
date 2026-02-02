<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Live Traffic Monitor
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Monitoring:
                <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs font-mono">
                   ACCOUNT
                </code>
            </p>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
        MONITORING
        </div>
    </div>

    @php 
        $chartId = 'trafficChart_' . uniqid();
    @endphp

    <div class="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-4" style="min-height: 400px;">
        <canvas id="{{ $chartId }}"></canvas>
    </div>

</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>

document.addEventListener("livewire:load", () => {
    window.initTrafficChart("{!! urldecode($pppoe) !!}", "{{ $chartId }}", "{{ $companyId }}");
});

document.addEventListener("livewire:navigated", () => {
    window.initTrafficChart("{!! urldecode($pppoe) !!}", "{{ $chartId }}", "{{ $companyId }}");
});






window.initTrafficChart = function(pppoeInterface, canvasId, companyId) {

    console.log("Initializing smooth chart for:", pppoeInterface, "Company ID:", companyId);

    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        setTimeout(() => window.initTrafficChart(pppoeInterface, canvasId, companyId), 300);
        return;
    }

    const ctx = canvas.getContext("2d");

    if (window.trafficChartInstance) {
        window.trafficChartInstance.destroy();
    }

    // Buffer store: timestamp + up/down speeds
    let points = [];

    const WINDOW_SECONDS = 20;   // Visible graph width (20 seconds)



    // ------------------------------
    // CREATE CHART (NO ANIMATION)
    // ------------------------------
    window.trafficChartInstance = new Chart(ctx, {
        type: "line",
        data: {
            datasets: [
                {
                    label: "↑ Download",
                    borderColor: "#ef4444",
                    backgroundColor: "rgba(239,68,68,0.15)",
                    data: [],
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: "↓ Upload",
                    borderColor: "#3b82f6",
                    backgroundColor: "rgba(59,130,246,0.15)",
                    data: [],
                    fill: true,
                    tension: 0.4,
                }
            ]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                x: {
                    type: "linear",
                    min: 0,
                    max: WINDOW_SECONDS,
                    ticks: { display: false },
                    grid: { display: true }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => v.toFixed(2) + " Mbps"
                    }
                }
            }
        }
    });




    // ------------------------------
    // FETCH ROUTER DATA EVERY SECOND
    // ------------------------------
    async function fetchData() {
        try {
            const resp = await fetch(`https://paybox.swiftelfiber.co.ke/api/live-traffic/${encodeURIComponent(pppoeInterface)}?company_id=${companyId}`);
            const d = await resp.json();

            const now = performance.now() / 1000;

            points.push({
                time: now,
                up: +(d.tx_bps / 1_000_000).toFixed(2),
                down: +(d.rx_bps / 1_000_000).toFixed(2),
            });

            // Keep only last WINDOW_SECONDS of points
            points = points.filter(p => now - p.time <= WINDOW_SECONDS);

        } catch (err) {
            console.error("Traffic API error:", err);
        }
    }

    fetchData();
    setInterval(fetchData, 2000);



    // -----------------------------------------
    // 60FPS REAL-TIME SMOOTH SCROLLING ANIMATION
    // -----------------------------------------
    function animate() {
        const now = performance.now() / 1000;

        const uploadData = points.map(p => ({
            x: WINDOW_SECONDS - (now - p.time),
            y: p.up
        }));

        const downloadData = points.map(p => ({
            x: WINDOW_SECONDS - (now - p.time),
            y: p.down
        }));

        window.trafficChartInstance.data.datasets[0].data = uploadData;
        window.trafficChartInstance.data.datasets[1].data = downloadData;

        window.trafficChartInstance.update();

        requestAnimationFrame(animate);
    }

    animate();
};

</script>
@endpush