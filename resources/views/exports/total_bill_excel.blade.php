<table>
    <thead>
        <tr>
            <th>Từ ngày</th>
            <th>Đến ngày</th>
            <th>Tổng dropship</th>
            <th>Chia dropship (mỗi người)</th>
            <th>Tổng chương trình</th>
            <th>Chia chương trình (mỗi người)</th>
            <th>Tổng chia tổng cộng</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $startDate->format('d/m/Y') }}</td>
            <td>{{ $endDate->format('d/m/Y') }}</td>
            <td>{{ number_format($total_dropship) }}</td>
            <td>{{ number_format($total_dropship_web) }}</td>
            <td>{{ number_format($total_program) }}</td>
            <td>{{ number_format($share_total_program) }}</td>
            <td>{{ number_format($total_dropship_web + $share_total_program) }}</td>
        </tr>
    </tbody>
</table>
