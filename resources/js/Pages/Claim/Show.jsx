import React, { useEffect, useState } from 'react'
import { Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { claim } = props

    const [sale, set_sale] = useState(null)
    const [customer, set_customer] = useState(null)
    const [c_date, set_c_date] = useState(new Date())
    const [reason, set_reason] = useState('')
    const [status, set_status] = useState('')
    const [items, set_items] = useState([])

    useEffect(() => {
        if (!isEmpty(claim)) {
            set_sale(claim.sale)
            set_customer(claim.customer)
            set_c_date(claim?.c_date ?? new Date())
            set_reason(claim.reason)
            set_status(claim.status)
            set_items(
                claim.items.map((item) => {
                    return {
                        ...item.product,
                        ...item,
                        price: item.sale_item.price,
                        qty_item: item.sale_item.qty,
                        qty_return: item.sale_item.qty_return,
                    }
                })
            )
        }
    }, [claim])

    return (
        <AuthenticatedLayout page={'System'} action={['Claim', claim?.c_code]}>
            <Head title="Claim" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <TextInput
                            value={sale?.s_code}
                            label="Penjualan"
                            readOnly={true}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <TextInput
                                value={c_date}
                                label="Tanggal"
                                readOnly={true}
                            />
                            <TextInput
                                placeholder="Customer"
                                value={customer?.name}
                                label="Customer"
                                readOnly={true}
                            />
                            <TextInput
                                value={status}
                                label={'Status'}
                                readOnly={true}
                            />
                            <TextInput
                                label={'Alasan Pengembalian'}
                                value={reason}
                                readOnly={true}
                            />
                        </div>
                        <div
                            className={`w-full border ${
                                errors.items
                                    ? 'border-red-600'
                                    : 'border-gray-400'
                            } rounded-xl px-2 py-4`}
                        >
                            <div className="overflow-x-auto">
                                <table className="table">
                                    <thead>
                                        <tr>
                                            <th>Part No</th>
                                            <th>Part Name</th>
                                            <th>Merk</th>
                                            <th>Harga</th>
                                            <th>QTY</th>
                                            <th>Claim QTY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {items.map((item) => (
                                            <tr key={item.id} className="hover">
                                                <td>{item.part_code}</td>
                                                <td>{item.name}</td>
                                                <td>{item?.brand?.name}</td>
                                                <td>{item.price}</td>
                                                <td>
                                                    {item.qty_item -
                                                        item.qty_return}
                                                </td>
                                                <td>{item.qty}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Link href={route('claims.index')}>
                                    <Button type="secondary">Kembali</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </AuthenticatedLayout>
    )
}
