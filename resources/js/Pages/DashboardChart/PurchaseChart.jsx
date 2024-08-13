import Chart from 'react-apexcharts'

export function PurchaseChart({ charts }) {
    const options = {
        chart: {
            id: 'visitor-bar',
        },
        grid: {
            show: false,
        },
        xaxis: {
            categories: charts.purchases.map((i) => i.date),
            lines: {
                show: false,
            },
        },
        yaxis: {
            lines: {
                show: false,
            },
        },
    }
    const series = [
        {
            name: 'Total',
            data: charts.purchases.map((i) => i.data.total),
        },
        {
            name: 'Jumlah Item',
            data: charts.purchases.map((i) => i.data.qty),
        },
    ]

    return (
        <div>
            <Chart
                options={options}
                series={series}
                type="bar"
                width="100%"
                height="200px"
            />
        </div>
    )
}
