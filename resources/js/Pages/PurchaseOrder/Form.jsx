import React, { useEffect, useState } from 'react'
import { router, Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import FormInputDate from '@/Components/DaisyUI/FormInputDate'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'
import SelectModalProduct from '../Product/SelectModal'
import { HiXMark } from 'react-icons/hi2'
import { formatIDR } from '@/utils'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import {
    purchase_order_status,
    purchase_order_status_draft,
    purchase_order_types,
} from '@/consts'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import SelectModalStoreOrder from '../StoreOrder/SelectModal'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { purchaseOrder } = props

    const [processing, set_processing] = useState(false)

    const [store_order, set_store_order] = useState(null)
    const [po_code, set_po_code] = useState('')
    const [po_date, set_po_date] = useState(new Date())
    const [type, set_type] = useState('')
    const [status, set_status] = useState(purchase_order_status_draft)
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [supplier, set_supplier] = useState(null)
    const [items, set_items] = useState([])

    const handleSetStoreOrder = (so) => {
        set_store_order(so)
        // handleSetSupplier(so.supplier)
        set_items(
            so.items.map((item) => {
                return {
                    ...item.product,
                    product_id: item.product.id,
                    qty: item.qty,
                    cost: item.cost,
                    subtotal: item.qty * item.cost,
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
                    if (name === 'qty') {
                        if (value < 0) {
                            return i
                        }
                        i['subtotal'] = Number(value * i['cost'])
                    }
                    i[name] = value
                }
                return i
            })
        )
    }

    const payload = {
        store_order_id: store_order?.id,
        po_date,
        type,
        status,
        address,
        note,
        supplier_id: supplier?.id,
        items,
    }

    const handleSubmit = () => {
        if (isEmpty(purchaseOrder) === false) {
            router.put(
                route('purchase-orders.update', purchaseOrder),
                payload,
                {
                    onStart: () => set_processing(true),
                    onFinish: (e) => {
                        set_processing(false)
                    },
                }
            )
            return
        }
        router.post(route('purchase-orders.store'), payload, {
            onStart: () => set_processing(true),
            onFinish: (e) => {
                set_processing(false)
            },
        })
    }

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

    const total = items.reduce((p, item) => p + item.subtotal, 0)

    return (
        <AuthenticatedLayout
            page={'System'}
            action={['Purchase Order', purchaseOrder?.po_code ?? 'Form']}
        >
            <Head title="Purchase Order" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <SelectModalStoreOrder
                            label="Order Toko"
                            placeholder="Pilih Order Toko"
                            onChange={handleSetStoreOrder}
                            error={errors.store_order_id}
                            value={store_order?.so_code}
                        />
                        <TextInput
                            label={'No PO'}
                            value={po_code}
                            placeholder="No PO (auto generate)"
                            readOnly={true}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <FormInputDate
                                value={po_date}
                                label={'Tanggal'}
                                onChange={(date) => set_po_date(date)}
                                error={errors.po_date}
                            />
                            <SelectOptionArray
                                value={type}
                                label={'Tipe'}
                                options={purchase_order_types}
                                onChange={(e) => set_type(e.target.value)}
                                error={errors.type}
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
                                onChange={(e) => set_note(e.target.value)}
                                error={errors.note}
                            />
                        </div>

                        <div className="w-full border border-gray-400 rounded-xl px-2 py-4">
                            <SelectModalProduct
                                placeholder="Pilih Part"
                                onChange={handleAddItem}
                                error={errors.items}
                                withCost={false}
                                withPrice={false}
                            />
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
                                            <th></th>
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
                                                {/* <td className="text-right">
                                                    {formatIDR(item.cost)}
                                                </td> */}
                                                {/* <td className="text-right">
                                                    {formatIDR(item.subtotal)}
                                                </td> */}
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
                        {/* <div className="w-full flex flex-row justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <div>TOTAL : </div>
                            <div>{formatIDR(total)}</div>
                        </div> */}
                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Button
                                    onClick={handleSubmit}
                                    processing={processing}
                                    type="primary"
                                >
                                    Simpan
                                </Button>
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
