import React, { useEffect, useState } from 'react'
import { router, Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import FormInputDate from '@/Components/DaisyUI/FormInputDate'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'
import { sale_status } from '@/consts'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import SelectModalProduct from '../Product/SelectModal'
import { HiXMark } from 'react-icons/hi2'
import { formatIDR } from '@/utils'
import SelectModalPurchase from '../Purchase/SelectModal'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { sale, ppn_percent } = props

    const [processing, setProcessing] = useState(false)

    const [purchase, set_purchase] = useState('')
    const [s_date, set_s_date] = useState(new Date())
    const [status, set_status] = useState('')
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [customer, set_customer] = useState(null)
    const [items, set_items] = useState([])

    const handleSetPurchase = (p) => {
        set_purchase(p)
        set_items(
            p.items.map((item) => {
                return {
                    ...item.product,
                    product_id: item.product.id,
                    qty: item.qty,
                    subtotal: item.product.price,
                    discount_percent: 0,
                    discount_amount: 0,
                    discount_total: 0,
                    subtotal_discount: item.product.price,
                    subtotal_net:
                        item.product.price -
                        item.product.price * (ppn_percent / 100),
                    subtotal_ppn: item.product.price * (ppn_percent / 100),
                }
            })
        )
    }

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
                subtotal: item.price,
                discount_percent: 0,
                discount_amount: 0,
                discount_total: 0,
                subtotal_discount: item.price,
                subtotal_net: item.price - item.price * (ppn_percent / 100),
                subtotal_ppn: item.price * (ppn_percent / 100),
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

                    i['subtotal'] = Number(i['qty'] * i['price'])
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
        purchase_id: purchase?.id,
        ppn_percent_applied: ppn_percent,
        s_date: s_date,
        status,
        address,
        note,
        customer_id: customer?.id,
        items,
        amount_cost: total_cost,
        amount_discount: discount,
        amount_net: total_net,
        amount_ppn: total_ppn,
    }

    const handleSubmit = () => {
        if (isEmpty(sale) === false) {
            router.put(route('sales.update', sale), payload, {
                onStart: () => setProcessing(true),
                onFinish: (e) => {
                    setProcessing(false)
                },
            })
            return
        }
        router.post(route('sales.store'), payload, {
            onStart: () => setProcessing(true),
            onFinish: (e) => {
                setProcessing(false)
            },
        })
    }

    useEffect(() => {
        if (!isEmpty(sale)) {
            set_purchase(sale.purchase)
            set_s_date(sale.s_date)
            set_status(sale.status)
            set_address(sale.address ?? '')
            set_note(sale.note ?? '')
            set_customer(sale.customer)
            set_items(
                sale.items.map((item) => {
                    return {
                        ...item,
                        ...item['product'],
                        subtotal: item['qty'] * item['price'],
                        price: item['price'],
                    }
                })
            )
        }
    }, [sale])

    return (
        <AuthenticatedLayout page={'System'} action={'Sale'}>
            <Head title="Sale" />

            <div>
                <Card>
                    <div className="flex flex-col gap-2 justify-between">
                        <SelectModalPurchase
                            label="Pembelian"
                            placeholder="Pilih Pembelian"
                            onChange={handleSetPurchase}
                            error={errors.purchase_id}
                            value={purchase?.p_code}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <FormInputDate
                                value={s_date}
                                label={'Tanggal'}
                                onChange={(date) => set_s_date(date)}
                                error={errors.po_date}
                            />
                            <SelectOptionArray
                                value={status}
                                label={'Status'}
                                options={sale_status}
                                onChange={(e) => set_status(e.target.value)}
                                error={errors.status}
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
                                                    <div className="w-full">
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
                                                            value={item.price}
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'price',
                                                                    value
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                </td>
                                                <td className="text-right  p-[5px]">
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
                                                <td className="text-right  p-[5px]">
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
                                <Link href={route('sales.index')}>
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
