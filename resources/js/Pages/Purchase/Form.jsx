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
import { purchase_order_status, purchase_order_types } from '@/consts'
import { formatIDR } from '@/utils'
import SelectModalPurchaseOrder from '../PurchaseOrder/SelectModal'
import { HiXMark } from 'react-icons/hi2'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { purchase, ppn_percent } = props

    const [processing, setProcessing] = useState(false)

    const [purchase_order, set_purchase_order] = useState('')
    const [p_date, set_p_date] = useState(new Date())
    const [status, set_status] = useState('')
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [supplier, set_supplier] = useState(null)
    const [items, set_items] = useState([])

    const handleSetPurchaseOrder = (po) => {
        set_purchase_order(po)
        handleSetSupplier(po.supplier)
        set_items(
            po.items.map((item) => {
                return {
                    ...item.product,
                    product_id: item.product.id,
                    qty: item.qty,
                    subtotal: item.product.cost,
                    discount_percent: 0,
                    discount_amount: 0,
                    discount_total: 0,
                    subtotal_discount: item.product.cost,
                    subtotal_net:
                        item.product.cost -
                        item.product.cost * (ppn_percent / 100),
                    subtotal_ppn: item.product.cost * (ppn_percent / 100),
                }
            })
        )
    }

    const handleSetSupplier = (supplier) => {
        set_supplier(supplier)
        set_address(supplier.address)
    }

    const handleAddItem = (item) => {
        const isExists = items.find((i) => item.id === i.id)
        if (isEmpty(isExists) === false) {
            return
        }

        set_items(
            items.concat({
                ...item,
                product_id: item.id,
                qty: 1,
                subtotal: item.cost,
                discount_percent: 0,
                discount_amount: 0,
                discount_total: 0,
                subtotal_discount: item.cost,
                subtotal_net: item.cost - item.cost * (ppn_percent / 100),
                subtotal_ppn: item.cost * (ppn_percent / 100),
            })
        )
    }

    const handleRemoveItem = (item) => {
        set_items(items.filter((i) => item.id !== i.id))
    }

    const handleChangeItem = (item, name, value) => {
        set_items(
            items.map((i) => {
                if (i.id === item.id) {
                    if (value < 1 && name === 'qty') {
                        return i
                    }

                    if (value < 0) {
                        return i
                    }

                    if (value > 100 && name === 'discount_percent') {
                        return i
                    }

                    i[name] = value

                    i['subtotal'] = Number(i['qty'] * i['cost'])
                    i['discount_total'] =
                        Number(i['discount_amount']) +
                        Number(i['subtotal'] * (i['discount_percent'] / 100))
                    i['subtotal_discount'] = Number(
                        i['subtotal'] - i['discount_total']
                    )
                    i['subtotal_net'] =
                        Number(i['subtotal_discount']) -
                        Number(i['subtotal_discount'] * (ppn_percent / 100))
                    i['subtotal_ppn'] = Number(
                        i['subtotal_discount'] * (ppn_percent / 100)
                    )
                }
                return i
            })
        )
    }

    const total_cost = items.reduce((p, item) => p + item.subtotal, 0)
    const discount = items.reduce((p, item) => p + item.discount_total, 0)
    const total = items.reduce((p, item) => p + item.subtotal_discount, 0)
    const total_net = items.reduce((p, item) => p + item.subtotal_net, 0)
    const total_ppn = items.reduce((p, item) => p + item.subtotal_ppn, 0)

    const payload = {
        purchase_order_id: purchase_order?.id,
        ppn_percent_applied: ppn_percent,
        p_date,
        status,
        address,
        note,
        supplier_id: supplier?.id,
        items,
        amount_cost: total_cost,
        amount_discount: discount,
        amount_net: total_net,
        amount_ppn: total_ppn,
    }

    const handleSubmit = () => {
        if (isEmpty(purchase) === false) {
            router.put(route('purchases.update', purchase), payload, {
                onStart: () => setProcessing(true),
                onFinish: (e) => {
                    setProcessing(false)
                },
            })
            return
        }
        router.post(route('purchases.store'), payload, {
            onStart: () => setProcessing(true),
            onFinish: (e) => {
                setProcessing(false)
            },
        })
    }

    useEffect(() => {
        if (!isEmpty(purchase)) {
            set_purchase_order(purchase.purchase_order)
            set_p_date(purchase.p_date)
            set_status(purchase.status)
            set_address(purchase.address ?? '')
            set_note(purchase.note ?? '')
            set_supplier(purchase.supplier)
            set_items(
                purchase.items.map((item) => {
                    return {
                        ...item,
                        ...item['product'],
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
                        <SelectModalPurchaseOrder
                            label="Pemesanan (PO)"
                            placeholder="Pilih Pemesanan (PO)"
                            onChange={handleSetPurchaseOrder}
                            error={errors.purchase_order_id}
                            value={purchase_order?.po_code}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <FormInputDate
                                value={p_date}
                                label={'Tanggal'}
                                onChange={(date) => set_p_date(date)}
                                error={errors.po_date}
                            />
                            <SelectOptionArray
                                value={status}
                                label={'Status'}
                                options={purchase_order_status}
                                onChange={(e) => set_status(e.target.value)}
                                error={errors.status}
                            />
                            <SelectModalInput
                                label="Nama Supplier"
                                value={supplier}
                                onChange={handleSetSupplier}
                                error={errors.supplier_id}
                                params={{
                                    table: 'suppliers',
                                    columns: 'id|code|name|address',
                                    display_name: 'name',
                                    orderby: 'created_at.asc',
                                }}
                            />
                            <TextInput
                                value={address}
                                label="Alamat"
                                onChange={(e) => set_address(e.target.value)}
                                error={errors.address}
                            />

                            <TextareaInput
                                value={note}
                                label={'Keterangan'}
                                onChange={(e) => set_note(e.target.value)}
                                error={errors.note}
                            />
                        </div>

                        <div className="w-full border border-gray-400 rounded-xl px-2 py-4">
                            <SelectModalProduct
                                placeholder="Pilih Part"
                                onChange={handleAddItem}
                                error={errors.items}
                            />
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
                                                Diskon (IDR)
                                            </th>
                                            <th className="text-right">
                                                Diskon (%)
                                            </th>
                                            <th className="text-right">
                                                Amount Diskon
                                            </th>
                                            <th className="text-right">
                                                Subtotal
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {items.map((item) => (
                                            <tr key={item.id} className="hover">
                                                <td>{item.part_code}</td>
                                                <td>{item.name}</td>
                                                <td>{item?.brand?.name}</td>
                                                <td className="text-right p-[5px]">
                                                    <div className="w-full min-w-[100px]">
                                                        <TextInput
                                                            type="number"
                                                            value={item.qty}
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'qty',
                                                                    value
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                </td>
                                                <td className="text-right justify-end p-[5px]">
                                                    <div className="w-full min-w-[100px]">
                                                        <TextInput
                                                            type="number"
                                                            value={item.cost}
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'cost',
                                                                    value
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                </td>
                                                <td className="text-right p-[5px]">
                                                    <div className="w-full min-w-[100px]">
                                                        <TextInput
                                                            type="number"
                                                            value={
                                                                item.discount_amount
                                                            }
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'discount_amount',
                                                                    value
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                </td>
                                                <td className="text-right p-[5px]">
                                                    <div className="w-full min-w-[100px]">
                                                        <TextInput
                                                            type="number"
                                                            value={
                                                                item.discount_percent
                                                            }
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'discount_percent',
                                                                    value
                                                                )
                                                            }
                                                        />
                                                    </div>
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
                                                <td>
                                                    <div
                                                        onClick={() =>
                                                            handleRemoveItem(
                                                                item
                                                            )
                                                        }
                                                    >
                                                        <HiXMark className="font-bold w-5 h-5 text-red-600" />
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div className="w-full flex flex-col justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <div className="w-full grid grid-cols-2 justify-between">
                                <div>Harga Jual : </div>
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
                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Button
                                    onClick={handleSubmit}
                                    processing={processing}
                                    type="primary"
                                >
                                    Simpan
                                </Button>
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
