{{--

    Component: chart-ads
    Hiển thị biểu đồ Line Chart.js cho quảng cáo
    Props: $labels, $datasets, $title, $canvasId, $dataColors
--}}

@props([
    'labels' => ["January","February","March","April","May","June","July","August","September","October"],
    'datasets' => [
        [
            'label' => 'Sales Analytics',
            'fill' => true,
            'tension' => 0.7,
            'borderWidth' => 2,
            'backgroundColor' => 'rgba(85,110,230,0.2)',
            'borderColor' => 'rgb(85,110,230)',
            'pointBorderColor' => 'rgb(85,110,230)',
            'pointBackgroundColor' => '#fff',
            'pointBorderWidth' => 1,
            'pointHoverRadius' => 5,
            'pointHoverBackgroundColor' => 'rgb(85,110,230)',
            'pointHoverBorderColor' => '#fff',
            'pointHoverBorderWidth' => 2,
            'pointRadius' => 1,
            'pointHitRadius' => 10,
            'data' => [65,59,80,81,56,55,40,55,30,80],
        ],
        [
            'label' => 'Monthly Earnings',
            'fill' => true,
            'tension' => 0.7,
            'borderWidth' => 2,
            'backgroundColor' => 'rgba(52,195,143,0.2)',
            'borderColor' => 'rgb(52,195,143)',
            'pointBorderColor' => 'rgb(52,195,143)',
            'pointBackgroundColor' => '#fff',
            'pointBorderWidth' => 1,
            'pointHoverRadius' => 5,
            'pointHoverBackgroundColor' => 'rgb(52,195,143)',
            'pointHoverBorderColor' => '#eef0f2',
            'pointHoverBorderWidth' => 2,
            'pointRadius' => 1,
            'pointHitRadius' => 10,
            'data' => [80,23,56,65,23,35,85,25,92,36],
        ],
    ],
    'title' => 'Line Chart',
    'canvasId' => 'lineChart',
    'dataColors' => '["--vz-primary-rgb, 0.2", "--vz-primary", "--vz-success-rgb, 0.2", "--vz-success"]',
])

<div style="height: 100%; width: 100%; justify-content: center; align-items: center;">
    <canvas style="height: 100%; position: absolute; left: 5; right: 0; bottom: 50% ; top: 50%;transform: translateY(-50%);" id="{{ $canvasId }}" class="chartjs-chart" data-colors='{{ $dataColors }}'></canvas>
</div>

@push('scripts')
<script>
// Hàm lấy màu từ thuộc tính data-colors
function getChartColorsArray(r){
    if(null!==document.getElementById(r)){
        var o="data-colors"+(("-"+document.documentElement.getAttribute("data-theme"))??""),
            o=document.getElementById(r).getAttribute(o)??document.getElementById(r).getAttribute("data-colors");
        if(o)
            return (o=JSON.parse(o)).map(function(r){
                var o=r.replace(" ","");
                return -1===o.indexOf(",")
                    ? getComputedStyle(document.documentElement).getPropertyValue(o)||o
                    : 2==(r=r.split(",")).length
                        ? "rgba("+getComputedStyle(document.documentElement).getPropertyValue(r[0])+","+r[1]+")"
                        : o
            });
        console.warn("data-colors attributes not found on",r)
    }
}

// Lưu ý: Nếu bạn truyền backgroundColor/borderColor là mảng (array) thì Chart.js sẽ tô màu từng cột/bar khác nhau.
// Nếu truyền string thì tất cả cột/bar sẽ cùng màu.
// Để truyền mảng màu cho từng cột, hãy truyền backgroundColor là array từ props/datasets.

document.addEventListener('DOMContentLoaded', function() {
    var islinechart = document.getElementById(@json($canvasId));
    var lineChartColor = getChartColorsArray(@json($canvasId));
    var datasets = @json($datasets);

    // Nếu muốn truyền màu cho từng cột/bar, hãy truyền backgroundColor là array từ ngoài vào datasets.
    // Nếu backgroundColor là string, sẽ áp dụng cho tất cả cột/bar.

    // Nếu datasets chưa có backgroundColor dạng array, sẽ gán màu động từ data-colors (nếu có)
    if (lineChartColor && islinechart) {
        islinechart.setAttribute("width", islinechart.parentElement.offsetWidth);

        // Chỉ gán màu nếu backgroundColor là string (không phải array)
        if (datasets.length > 0 && lineChartColor.length >= 2) {
            if (!Array.isArray(datasets[0].backgroundColor)) {
                datasets[0].backgroundColor = lineChartColor[0];
            }
            if (!Array.isArray(datasets[0].borderColor)) {
                datasets[0].borderColor = lineChartColor[1];
            }
            datasets[0].pointBorderColor = lineChartColor[1];
            datasets[0].pointHoverBackgroundColor = lineChartColor[1];
        }
        if (datasets.length > 1 && lineChartColor.length >= 4) {
            if (!Array.isArray(datasets[1].backgroundColor)) {
                datasets[1].backgroundColor = lineChartColor[2];
            }
            if (!Array.isArray(datasets[1].borderColor)) {
                datasets[1].borderColor = lineChartColor[3];
            }
            datasets[1].pointBorderColor = lineChartColor[3];
            datasets[1].pointHoverBackgroundColor = lineChartColor[3];
        }
    }

    if (islinechart) {
        var lineChart = new Chart(islinechart, {
            type: "bar",
            data: {
                labels: @json($labels),
                datasets: datasets
            },
            options: {
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                x: { ticks: { font: { family: "Poppins" } } },
                y: { ticks: { font: { family: "Poppins" } } },
                plugins: {
                    legend: { labels: { font: { family: "Poppins" } } },
                    title: { display: true, text: @json($title) }
                }
            }
        });
    }
});
</script>
@endpush