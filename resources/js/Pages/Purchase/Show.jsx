import React, { useEffect, useState } from 'react'
import { router, Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import FormInputDate from '@/Components/DaisyUI/FormInputDate'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'
import SelectModalProduct from '../Product/SelectModal'
import { purchase_order_status, purchase_order_status_draft } from '@/consts'
import { formatIDR } from '@/utils'
import SelectModalPurchaseOrder from '../PurchaseOrder/SelectModal'
import { HiXMark } from 'react-icons/hi2'
import Checkbox from '@/Components/DaisyUI/Checkbox'

export default function Show(props) {
    const {
        props: { errors },
    } = usePage()
    const { purchase, ppn_percent } = props

    const [use_ppn, set_use_ppn] = useState(true)
    const [use_ppn_percent, set_use_ppn_percent] = useState(ppn_percent)
    const [purchase_order, set_purchase_order] = useState('')
    const [p_date, set_p_date] = useState(new Date())
    const [status, set_status] = useState(purchase_order_status_draft)
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [supplier, set_supplier] = useState(null)
    const [items, set_items] = useState([])

    const total_cost = items.reduce((p, item) => p + item.subtotal, 0)
    const discount = items.reduce((p, item) => p + item.discount_total, 0)
    const total = items.reduce((p, item) => p + item.subtotal_discount, 0)
    const total_net = use_ppn
        ? items.reduce((p, item) => p + item.subtotal_net, 0)
        : 0
    const total_ppn = items.reduce((p, item) => p + item.subtotal_ppn, 0)

    useEffect(() => {
        if (!isEmpty(purchase)) {
            if (purchase.ppn_percent_applied === 1) {
                set_use_ppn(false)
            }
            set_use_ppn_percent(purchase.ppn_percent_applied)
            set_purchase_order(purchase.purchase_order)
            set_p_date(purchase.p_date)
            set_status(purchase.status)
            set_address(purchase.address ?? '')
            set_note(purchase.note ?? '')
            set_supplier(purchase.supplier)
            set_items(
                purchase.items.map((item) => {
                    return {
                        ...item['product'],
                        ...item,
                        subtotal: item['qty'] * item['cost'],
                        cost: item['cost'],
                    }
                })
            )
        }
    }, [purchase])

    return (
        <AuthenticatedLayout
            page={'System'}
            action={['Purchase', purchase?.p_code]}
        >
            <Head title="Purchase" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <TextInput
                            label="Pemesanan (PO)"
                            placeholder="Pilih Pemesanan (PO)"
                            readOnly={true}
                            value={purchase_order?.po_code}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <TextInput
                                value={p_date}
                                label={'Tanggal'}
                                readOnly={true}
                            />
                            <TextInput
                                label="Nama Supplier"
                                value={supplier?.name}
                                readOnly={true}
                            />
                            <TextInput
                                value={address}
                                label="Alamat"
                                readOnly={true}
                            />

                            <TextareaInput
                                value={note}
                                label={'Keterangan'}
                                readOnly={true}
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
                                            <th className="text-right">
                                                Harga
                                            </th>
                                            <th className="text-right">
                                                Diskon 1 (%)
                                            </th>
                                            <th className="text-right">
                                                Diskon 2 (%)
                                            </th>
                                            <th className="text-right">
                                                Amount Diskon
                                            </th>
                                            <th className="text-right">
                                                Subtotal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {items.map((item) => (
                                            <tr key={item.id} className="hover">
                                                <td>{item.part_code}</td>
                                                <td>{item.name}</td>
                                                <td>{item?.brand?.name}</td>
                                                <td className="text-right p-[5px]">
                                                    {formatIDR(item.qty)}
                                                </td>
                                                <td className="text-right justify-end p-[5px]">
                                                    {formatIDR(item.cost)}
                                                </td>
                                                <td className="text-right p-[5px]">
                                                    {formatIDR(
                                                        item.discount_percent_2
                                                    )}
                                                </td>
                                                <td className="text-right p-[5px]">
                                                    {formatIDR(
                                                        item.discount_percent_1
                                                    )}
                                                </td>
                                                <td className="text-right">
                                                    {formatIDR(
                                                        item.discount_total
                                                    )}
                                                </td>
                                                <td className="text-right">
                                                    {formatIDR(
                                                        item.subtotal_discount
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div className="w-full flex flex-col justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>Harga Beli : </div>
                                <div className="text-right">
                                    {formatIDR(total_cost)}
                                </div>
                            </div>
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>Diskon : </div>
                                <div className="text-right">
                                    {formatIDR(discount)}
                                </div>
                            </div>
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>Grand Total : </div>
                                <div className="text-right">
                                    {formatIDR(total)}
                                </div>
                            </div>
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>DPP : </div>
                                <div className="text-right">
                                    {formatIDR(total_net)}
                                </div>
                            </div>
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>PPN : </div>
                                <div className="text-right">
                                    {formatIDR(total_ppn)}
                                </div>
                            </div>
                        </div>
                        {/* <div className="w-full flex flex-col justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <Checkbox label="Menggunakan PPN" value={use_ppn} />
                        </div> */}
                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Link href={route('purchases.index')}>
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
