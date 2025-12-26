{{--
    Component: chart-poler-area
    Hiển thị biểu đồ Polar Area Chart.js
    Props: $labels, $datasetLabel, $data, $backgroundColors, $title
--}}

@props([
    'labels' => ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
    'datasetLabel' => 'Dataset 1',
    'data' => [12, 19, 3, 5, 2],
    'backgroundColors' => [
        'rgba(255, 99, 132, 0.5)',
        'rgba(255, 159, 64, 0.5)',
        'rgba(255, 205, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(54, 162, 235, 0.5)',
    ],
    'title' => 'Chart.js Polar Area Chart',
    'canvasId' => 'polarAreaChart',
])

<div style="width:100%;max-width:400px;">
    <canvas id="{{ $canvasId }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById(@json($canvasId));
        if (!ctx) return;
        const config = {
            type: 'polarArea',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: @json($datasetLabel),
                    data: @json($data),
                    backgroundColor: @json($backgroundColors),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: @json($title) }
                }
            }
        };
        new Chart(ctx, config);
    });
</script>
@endpush