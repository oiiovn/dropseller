<table>
    <thead>
        <tr>
            <th>Từ ngày</th>
            <th>Đến ngày</th>
            <th>Tổng tiền dropship</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $startDate->format('d/m/Y') }}</td>
            <td>{{ $endDate->format('d/m/Y') }}</td>
            <td>{{ number_format($total_dropship) }} VND</td>
        </tr>
    </tbody>
</table>
