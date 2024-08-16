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
    store_order_status,
    store_order_status_draft,
    store_order_types,
} from '@/consts'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { storeOrder } = props

    const [processing, set_processing] = useState(false)

    const [so_code, set_so_code] = useState('')
    const [so_date, set_so_date] = useState(new Date())
    const [type, set_type] = useState('')
    const [status, set_status] = useState(store_order_status_draft)
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [customer, set_customer] = useState(null)
    const [items, set_items] = useState([])

    const handleSetCustomer = (customer) => {
        set_customer(customer)
        set_address(customer.address)
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
        so_date: so_date,
        type,
        status,
        address,
        note,
        customer_id: customer?.id,
        items,
    }

    const handleSubmit = () => {
        if (isEmpty(storeOrder) === false) {
            router.put(route('store-orders.update', storeOrder), payload, {
                onStart: () => set_processing(true),
                onFinish: (e) => {
                    set_processing(false)
                },
            })
            return
        }
        router.post(route('store-orders.store'), payload, {
            onStart: () => set_processing(true),
            onFinish: (e) => {
                set_processing(false)
            },
        })
    }

    useEffect(() => {
        if (!isEmpty(storeOrder)) {
            set_so_code(storeOrder.so_code)
            set_so_date(storeOrder.so_date)
            set_type(storeOrder.type)
            set_status(storeOrder.status)
            set_address(storeOrder.address)
            set_note(storeOrder.note)
            set_customer(storeOrder.customer)
            set_items(
                storeOrder.items.map((item) => {
                    return {
                        ...item,
                        ...item['product'],
                        subtotal: item['qty'] * item['cost'],
                    }
                })
            )
        }
    }, [storeOrder])

    const total = items.reduce((p, item) => p + item.subtotal, 0)

    return (
        <AuthenticatedLayout
            page={'System'}
            action={['Order Toko', storeOrder?.so_code ?? 'Form']}
        >
            <Head title="Order Toko" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <TextInput
                            label={'No'}
                            value={so_code}
                            placeholder="No (auto generate)"
                            readOnly={true}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <FormInputDate
                                value={so_date}
                                label={'Tanggal'}
                                onChange={(date) => set_so_date(date)}
                                error={errors.po_date}
                            />
                            <SelectOptionArray
                                value={type}
                                label={'Tipe'}
                                options={store_order_types}
                                onChange={(e) => set_type(e.target.value)}
                                error={errors.type}
                            />
                            <SelectModalInput
                                label="Nama Customer"
                                value={customer}
                                onChange={handleSetCustomer}
                                error={errors.customer_id}
                                params={{
                                    table: 'customers',
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
                                options={store_order_status}
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
                                <Link href={route('store-orders.index')}>
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
