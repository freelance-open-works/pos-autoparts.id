import React, { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { usePrevious } from 'react-use'
import { Head } from '@inertiajs/react'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Pagination from '@/Components/DaisyUI/Pagination'
import SearchInput from '@/Components/DaisyUI/SearchInput'
import Card from '@/Components/DaisyUI/Card'
import { formatDate, formatIDR } from '@/utils'
import FormInputDateRanger from '@/Components/DaisyUI/FormInputDateRange'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'
import Button from '@/Components/DaisyUI/Button'
import { HiXCircle } from 'react-icons/hi2'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [dates, setDates] = useState({})
    const [customer, setCustomer] = useState('')
    const [search, setSearch] = useState('')
    const preValue = usePrevious(`${search}${customer}${dates}`)

    const params = { q: search, customer_id: customer?.id, ...dates }
    useEffect(() => {
        if (preValue) {
            router.get(route(route().current()), params, {
                replace: true,
                preserveState: true,
            })
        }
    }, [search, customer, dates])

    return (
        <AuthenticatedLayout page={'System'} action={'Report Sale'}>
            <Head title="Report Sale" />

            <div>
                <Card>
                    <div className="w-full flex justify-between">
                        <div className="w-full grid grid-cols-4 gap-2">
                            <SearchInput
                                onChange={(e) => setSearch(e.target.value)}
                                value={search}
                            />
                            <FormInputDateRanger
                                value={dates}
                                placeholder={'Filter Date'}
                                onChange={(date) => setDates(date)}
                            />
                            <div className="flex flex-row gap-1">
                                <SelectModalInput
                                    placeholder="Filter Customer"
                                    value={customer}
                                    onChange={(e) => setCustomer(e)}
                                    params={{
                                        table: 'customers',
                                        columns: 'id|code|name|address',
                                        display_name: 'name',
                                        orderby: 'created_at.asc',
                                    }}
                                />
                                <Button onClick={(e) => setCustomer('')}>
                                    <HiXCircle className="h-5 w-5" />
                                </Button>
                            </div>
                            <div className="flex justify-end">
                                <a
                                    href={route('report.sales.export', params)}
                                    target="_blank"
                                >
                                    <Button>Export</Button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="table mt-6">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Tanggal</th>
                                    <th>Customer Kode</th>
                                    <th>Customer Nama</th>
                                    <th>Salesman Kode</th>
                                    <th>Salesman Nama</th>
                                    <th>Part No</th>
                                    <th>Part Nama</th>
                                    <th>Qty</th>
                                    <th>Harga Jual</th>
                                    <th>Total Diskon</th>
                                    <th>DPP</th>
                                    <th>PPN</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{item.sale.s_code}</td>
                                        <td>{formatDate(item.sale.s_date)}</td>
                                        <td>{item.sale.customer.code}</td>
                                        <td>{item.sale.customer.name}</td>
                                        <td>
                                            {item.sale.creator.fields?.code}
                                        </td>
                                        <td>{item.sale.creator.name}</td>
                                        <td>{item.product.code}</td>
                                        <td>{item.product.name}</td>
                                        <td>{formatIDR(item.qty)}</td>
                                        <td>{formatIDR(item.price)}</td>
                                        <td>
                                            {formatIDR(item.discount_total)}
                                        </td>
                                        <td>{formatIDR(item.subtotal_net)}</td>
                                        <td>{formatIDR(item.subtotal_ppn)}</td>
                                        <td>
                                            {formatIDR(item.subtotal_discount)}
                                        </td>
                                        <td>{item.sale.status}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div className="w-full overflow-x-auto flex lg:justify-center">
                        <Pagination links={links} params={params} />
                    </div>
                </Card>
            </div>
        </AuthenticatedLayout>
    )
}
