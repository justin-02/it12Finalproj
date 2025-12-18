<div class="employee-performance">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4>{{ $employee->name }} - Performance Overview</h4>
            <p class="text-muted">Last 30 days performance data</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-secondary fs-6">{{ ucfirst($employee->role) }}</span>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>₱{{ number_format($performanceData->sum('total_sales'), 2) }}</h4>
                    <p class="mb-0">Total Sales (30 days)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $performanceData->sum('transactions') }}</h4>
                    <p class="mb-0">Total Transactions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>
                        @if($performanceData->sum('transactions') > 0)
                            ₱{{ number_format($performanceData->sum('total_sales') / $performanceData->sum('transactions'), 2) }}
                        @else
                            ₱0.00
                        @endif
                    </h4>
                    <p class="mb-0">Average Sale</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h4>{{ $performanceData->count() }}</h4>
                    <p class="mb-0">Active Days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th class="text-end">Transactions</th>
                    <th class="text-end">Total Sales</th>
                    <th class="text-end">Average Sale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($performanceData as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                    <td class="text-end">{{ $data->transactions }}</td>
                    <td class="text-end text-success">₱{{ number_format($data->total_sales, 2) }}</td>
                    <td class="text-end text-info">
                        ₱{{ number_format($data->transactions > 0 ? $data->total_sales / $data->transactions : 0, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        No performance data available for the last 30 days.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($performanceData->count() > 0)
            <tfoot class="table-secondary">
                <tr>
                    <th>Total</th>
                    <th class="text-end">{{ $performanceData->sum('transactions') }}</th>
                    <th class="text-end text-success">₱{{ number_format($performanceData->sum('total_sales'), 2) }}</th>
                    <th class="text-end text-info">
                        ₱{{ number_format($performanceData->sum('transactions') > 0 ? $performanceData->sum('total_sales') / $performanceData->sum('transactions') : 0, 2) }}
                    </th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Performance Chart -->
    <div class="mt-4 p-3 bg-light rounded">
        <h6>Sales Performance Chart</h6>
        <div class="chart-wrapper mt-3 mb-3" style="height:200px; position:relative;">
            <canvas id="employeePerformanceChart-{{ $employee->id }}"></canvas>
        </div>
        <p class="text-muted small mb-0">Daily sales for the last {{ $performanceData->count() }} days.</p>
    </div>

    {{-- Chart.js CDN (lightweight) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Ensure canvas fills wrapper and doesn't overflow */
        .chart-wrapper canvas { width: 100% !important; height: 100% !important; }

        /* Constrain modal body height so content scrolls instead of overlapping */
        #employeePerformanceModal .modal-body {
            max-height: calc(80vh - 200px);
            overflow-y: auto;
        }
    </style>
    <script>
        (function(){
            try {
                const labels = {!! json_encode($performanceData->map(function($d){ return \Carbon\Carbon::parse($d->date)->format('M d'); })->values()) !!};
                const sales = {!! json_encode($performanceData->map(function($d){ return (float) $d->total_sales; })->values()) !!};

                const canvasId = 'employeePerformanceChart-{{ $employee->id }}';
                const ctxEl = document.getElementById(canvasId);
                if (!ctxEl) return;

                // destroy existing instance when modal is reopened to prevent overlap
                try {
                    if (window['employeeChart_{{ $employee->id }}']) {
                        window['employeeChart_{{ $employee->id }}'].destroy();
                        window['employeeChart_{{ $employee->id }}'] = null;
                    }
                } catch (e) { /* ignore */ }

                const ctx = ctxEl.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height || 260);
                gradient.addColorStop(0, 'rgba(45,90,61,0.25)');
                gradient.addColorStop(1, 'rgba(45,90,61,0)');

                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Sales',
                            data: sales,
                            backgroundColor: gradient,
                            borderColor: '#2d5a3d',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2d5a3d',
                            fill: true,
                            tension: 0.25,
                            pointRadius: 3,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { display: true, grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } },
                            y: { display: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: function(value){ return '₱' + Number(value).toFixed(2); } } }
                        }
                    }
                });

                // store instance globally to allow destruction later
                window['employeeChart_{{ $employee->id }}'] = chart;

                // ensure chart is destroyed when the modal is hidden to avoid overlapping redraws
                try {
                    var modalEl = document.getElementById('employeePerformanceModal');
                    if (modalEl) {
                        modalEl.addEventListener('hidden.bs.modal', function(){
                            try {
                                if (window['employeeChart_{{ $employee->id }}']) {
                                    window['employeeChart_{{ $employee->id }}'].destroy();
                                    window['employeeChart_{{ $employee->id }}'] = null;
                                }
                            } catch(e) { }
                        });
                    }
                } catch(e) {}

            } catch (e) {
                console.error('Failed to render employee performance chart', e);
            }
        })();
    </script>
</div>