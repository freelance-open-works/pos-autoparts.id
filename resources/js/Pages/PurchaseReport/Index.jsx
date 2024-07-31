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
    const [supplier, setSupplier] = useState('')
    const [search, setSearch] = useState('')
    const preValue = usePrevious(`${search}${supplier}${dates}`)

    const params = { q: search, supplier_id: supplier?.id, ...dates }
    useEffect(() => {
        if (preValue) {
            router.get(route(route().current()), params, {
                replace: true,
                preserveState: true,
            })
        }
    }, [search, supplier, dates])

    return (
        <AuthenticatedLayout page={'System'} action={'Report Purchase'}>
            <Head title="Report Purchase" />

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
                                    placeholder="Filter Supplier"
                                    value={supplier}
                                    onChange={(e) => setSupplier(e)}
                                    params={{
                                        table: 'suppliers',
                                        columns: 'id|code|name|address',
                                        display_name: 'name',
                                        orderby: 'created_at.asc',
                                    }}
                                />
                                <Button onClick={(e) => setSupplier('')}>
                                    <HiXCircle className="h-5 w-5" />
                                </Button>
                            </div>
                            <div className="flex justify-end">
                                <a
                                    href={route(
                                        'report.purchases.export',
                                        params
                                    )}
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
                                    <th>Supplier Kode</th>
                                    <th>Supplier Nama</th>
                                    <th>Salesman Kode</th>
                                    <th>Salesman Nama</th>
                                    <th>Part No</th>
                                    <th>Part Nama</th>
                                    <th>Qty</th>
                                    <th>Harga Beli</th>
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
                                        <td>{item.purchase.p_code}</td>
                                        <td>
                                            {formatDate(item.purchase.p_date)}
                                        </td>
                                        <td>{item.purchase.supplier.code}</td>
                                        <td>{item.purchase.supplier.name}</td>
                                        <td>
                                            {item.purchase.creator.fields?.code}
                                        </td>
                                        <td>{item.purchase.creator.name}</td>
                                        <td>{item.product.part_code}</td>
                                        <td>{item.product.name}</td>
                                        <td>{formatIDR(item.qty)}</td>
                                        <td>{formatIDR(item.cost)}</td>
                                        <td>
                                            {formatIDR(item.discount_total)}
                                        </td>
                                        <td>{formatIDR(item.subtotal_net)}</td>
                                        <td>{formatIDR(item.subtotal_ppn)}</td>
                                        <td>
                                            {formatIDR(item.subtotal_discount)}
                                        </td>
                                        <td>{item.purchase.status}</td>
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
