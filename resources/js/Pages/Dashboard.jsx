import React, { useEffect, useState } from 'react'
import { Head, router } from '@inertiajs/react'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import DummyDashbord from '@/Components/Dummy/Dashboard'
import { formatIDR } from '@/utils'
import { SaleChart } from './DashboardChart/SaleChart'
import { PurchaseChart } from './DashboardChart/PurchaseChart'
import FormInputDateRanger from '@/Components/DaisyUI/FormInputDateRange'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import { usePrevious } from 'react-use'

export default function Dashboard(props) {
    const [dates, set_dates] = useState({
        startDate: props.charts.start_date,
        endDate: props.charts.end_date,
    })
    const [brand, set_brand] = useState('')
    const [user, set_user] = useState('')
    const [type, set_type] = useState('')

    const preValue = usePrevious(`${brand}${user}${type}${dates}`)

    useEffect(() => {
        if (preValue) {
            router.get(
                route(route().current()),
                { brand, user, type, ...dates },
                {
                    replace: true,
                    preserveState: true,
                }
            )
        }
    }, [brand, user, type, dates])

    return (
        <AuthenticatedLayout page={'Dashboard'} action={''}>
            <Head title="Dashboard" />

            <div>
                <div className="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 py-2 gap-2">
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Roles</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.role_count)}{' '}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Users</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.user_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Customer</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.customer_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Supplier</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.supplier_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Product</div>
                            <div className="stat-value text-primary text-3xl">
                                {formatIDR(props.product_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">
                                Total Penjualan {props.month}
                            </div>
                            <div className="stat-value text-primary text-3xl">
                                {formatIDR(props.total_sale_month)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">
                                Total penjualan Hari ini
                            </div>
                            <div className="stat-value text-primary text-3xl">
                                {formatIDR(props.total_sale_today)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">
                                Barang terjual hari ini
                            </div>
                            <div className="stat-value text-primary text-3xl">
                                {formatIDR(props.items_sale_today)}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="card bg-base-100 w-full p-4 my-2">
                    <div>
                        <FormInputDateRanger
                            value={dates}
                            label={'Date Range'}
                            onChange={(date) => set_dates(date)}
                        />
                    </div>
                    <div className="grid grid-cols-3 gap-1">
                        {/* brand */}
                        <SelectOptionArray
                            label="Merk"
                            value={brand}
                            options={props.brands.map((i) => i.name)}
                            onChange={(e) => set_brand(e.target.value)}
                        />
                        {/* users */}
                        <SelectOptionArray
                            label="Sales"
                            value={user}
                            options={props.users.map((i) => i.name)}
                            onChange={(e) => set_user(e.target.value)}
                        />
                        {/* inout */}
                        <SelectOptionArray
                            label="Type"
                            value={type}
                            options={props.types}
                            onChange={(e) => set_type(e.target.value)}
                        />
                    </div>
                </div>
                <div className="card bg-base-100 w-full p-4 my-2">
                    <div className="font-bold text-lg pb-2">Penjualan</div>
                    <SaleChart charts={props.charts} />
                </div>
                <div className="card bg-base-100 w-full p-4 my-2">
                    <div className="font-bold text-lg pb-2">Pembelian</div>
                    <PurchaseChart charts={props.charts} />
                </div>
                {/* daftar product terlaris / top 10 */}
                <div className="card bg-base-100 w-full p-4">
                    <div className="font-bold text-lg pb-2">
                        Barang paling laku terjual
                    </div>
                    <table className="table hover">
                        <thead>
                            <tr>
                                <th>Part Code</th>
                                <th>Name</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {props.top_products.map((p) => (
                                <tr key={p.id}>
                                    <td>{p.part_code}</td>
                                    <td>{p.name}</td>
                                    <td>{formatIDR(p.most_qty)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
