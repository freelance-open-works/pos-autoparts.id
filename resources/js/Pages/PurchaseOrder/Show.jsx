import React, { useEffect, useState } from 'react'
import { Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import { purchase_order_status_draft } from '@/consts'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { purchaseOrder } = props

    const [store_order, set_store_order] = useState(null)
    const [po_code, set_po_code] = useState('')
    const [po_date, set_po_date] = useState(new Date())
    const [type, set_type] = useState('')
    const [status, set_status] = useState(purchase_order_status_draft)
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [supplier, set_supplier] = useState(null)
    const [items, set_items] = useState([])

    useEffect(() => {
        if (!isEmpty(purchaseOrder)) {
            set_store_order(purchaseOrder.store_order)
            set_po_code(purchaseOrder.po_code)
            set_po_date(purchaseOrder.po_date)
            set_type(purchaseOrder.type)
            set_status(purchaseOrder.status)
            set_address(purchaseOrder.address)
            set_note(purchaseOrder.note)
            set_supplier(purchaseOrder.supplier)
            set_items(
                purchaseOrder.items.map((item) => {
                    return {
                        ...item,
                        ...item['product'],
                        subtotal: item['qty'] * item['cost'],
                    }
                })
            )
        }
    }, [purchaseOrder])

    // const total = items.reduce((p, item) => p + item.subtotal, 0)

    return (
        <AuthenticatedLayout page={'System'} action={'Purchase Order'}>
            <Head title="Purchase Order" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <TextInput
                            label={'Order Toko'}
                            value={store_order?.so_code}
                            placeholder="Order Toko"
                            readOnly={true}
                        />
                        <TextInput
                            label={'No PO'}
                            value={po_code}
                            placeholder="No PO (auto generate)"
                            readOnly={true}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <TextInput
                                value={po_date}
                                label={'Tanggal'}
                                readOnly={true}
                                error={errors.po_date}
                            />
                            <TextInput
                                value={type}
                                label={'Tipe'}
                                readOnly={true}
                                error={errors.type}
                            />
                            <TextInput
                                label="Nama Supplier"
                                value={supplier?.name}
                                readOnly={true}
                                error={errors.supplier_id}
                            />
                            <TextInput
                                value={address}
                                label="Alamat"
                                TextInput
                                error={errors.address}
                            />
                            {/* <SelectOptionArray
                                value={status}
                                label={'Status'}
                                options={purchase_order_status}
                                onChange={(e) => set_status(e.target.value)}
                                error={errors.status}
                            /> */}
                            <TextareaInput
                                value={note}
                                label={'Keterangan'}
                                TextInput
                                error={errors.note}
                            />
                        </div>

                        <div className="w-full border border-gray-400 rounded-xl px-2 py-4">
                            <div className="overflow-x-auto">
                                <table className="table">
                                    <thead>
                                        <tr>
                                            <th>Part No</th>
                                            <th>Part Name</th>
                                            <th>Merk</th>
                                            <th>QTY</th>
                                            {/* <th className="text-right">
                                                Harga
                                            </th> */}
                                            {/* <th className="text-right">
                                                Subtotal
                                            </th> */}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {items.map((item) => (
                                            <tr key={item.id} className="hover">
                                                <td>{item.part_code}</td>
                                                <td>{item.name}</td>
                                                <td>{item?.brand?.name}</td>
                                                <td>
                                                    <div className="w-[75px]">
                                                        {item.qty}
                                                    </div>
                                                </td>
                                                {/* <td className="text-right">
                                                    {formatIDR(item.cost)}
                                                </td> */}
                                                {/* <td className="text-right">
                                                    {formatIDR(item.subtotal)}
                                                </td> */}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {/* <div className="w-full flex flex-row justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <div>TOTAL : </div>
                            <div>{formatIDR(total)}</div>
                        </div> */}
                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Link href={route('purchase-orders.index')}>
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
