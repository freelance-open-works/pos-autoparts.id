import Chart from 'react-apexcharts'

export function SaleChart({ charts }) {
    const options = {
        chart: {
            id: 'visitor-bar',
        },
        grid: {
            show: false,
        },
        xaxis: {
            categories: charts.sales.map((i) => i.date),
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
            data: charts.sales.map((i) => i.data.total),
        },
        {
            name: 'Jumlah Item',
            data: charts.sales.map((i) => i.data.qty),
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
