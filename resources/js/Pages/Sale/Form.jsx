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
import { sale_status, sale_status_draft } from '@/consts'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import SelectModalProduct from '../Product/SelectModal'
import { HiXMark } from 'react-icons/hi2'
import { formatIDR } from '@/utils'
import SelectModalPurchase from '../Purchase/SelectModal'
import Checkbox from '@/Components/DaisyUI/Checkbox'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { sale, ppn_percent } = props

    const [processing, setProcessing] = useState(false)

    const [use_ppn, set_use_ppn] = useState(true)
    const [use_ppn_percent, set_use_ppn_percent] = useState(ppn_percent)
    const [purchase, set_purchase] = useState('')
    const [s_date, set_s_date] = useState(new Date())
    const [status, set_status] = useState(sale_status_draft)
    const [address, set_address] = useState('')
    const [note, set_note] = useState('')
    const [customer, set_customer] = useState(null)
    const [items, set_items] = useState([])

    const formatItem = (i, ppn) => {
        i['subtotal'] = Number(i['qty'] * i['price'])
        i['discount_total'] =
            Number(i['subtotal'] * (i['discount_percent_2'] / 100)) +
            Number(i['subtotal'] * (i['discount_percent_1'] / 100))
        i['subtotal_discount'] = Number(i['subtotal'] - i['discount_total'])
        i['subtotal_net'] = Number(i['subtotal'] / ppn)
        i['subtotal_ppn'] = Number(i['subtotal'] - i['subtotal_net'])
        return i
    }

    const handleSetPpn = () => {
        set_use_ppn(!use_ppn)
        let ppn_use = ppn_percent
        if (!use_ppn === false) {
            ppn_use = 1
        }
        set_use_ppn_percent(ppn_use)
        // update items
        set_items(
            items.map((i) => {
                return formatItem(i, ppn_use)
            })
        )
    }

    const handleSetPurchase = (p) => {
        set_purchase(p)
        set_items(
            p.items.map((item) => {
                return {
                    // HERE
                    ...item.product,
                    product_id: item.product.id,
                    qty: item.qty,
                    subtotal: item.product.price,
                    discount_percent_1: 0,
                    discount_percent_2: 0,
                    discount_total: 0,
                    subtotal_discount: item.product.price,
                    subtotal_net: item.product.price / use_ppn_percent,
                    subtotal_ppn:
                        item.product.price -
                        item.product.price / use_ppn_percent,
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
                // HERE
                ...item,
                product_id: item.id,
                qty: 1,
                subtotal: item.price,
                discount_percent_1: 0,
                discount_percent_2: 0,
                discount_total: 0,
                subtotal_discount: item.price,
                subtotal_net: item.price / use_ppn_percent,
                subtotal_ppn: item.price - item.price / use_ppn_percent,
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

                    if (value > 100 && name === 'discount_percent_1') {
                        return i
                    }

                    if (value > 100 && name === 'discount_percent_2') {
                        return i
                    }

                    i[name] = value
                    i = formatItem(i, use_ppn_percent)
                }
                return i
            })
        )
    }

    const total_cost = items.reduce((p, item) => p + item.subtotal, 0)
    const discount = items.reduce((p, item) => p + item.discount_total, 0)
    const total = items.reduce((p, item) => p + item.subtotal_discount, 0)
    const total_net = use_ppn
        ? items.reduce((p, item) => p + item.subtotal_net, 0)
        : 0
    const total_ppn = items.reduce((p, item) => p + item.subtotal_ppn, 0)

    const payload = {
        purchase_id: purchase?.id,
        ppn_percent_applied: use_ppn_percent,
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
            if (sale.ppn_percent_applied === 1) {
                set_use_ppn(false)
            }
            set_use_ppn_percent(sale.ppn_percent_applied)
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
        <AuthenticatedLayout page={'System'} action={['Sale', sale?.s_code ?? 'Form']}>
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
                                error={errors.s_date}
                            />
                            {/* <SelectOptionArray
                                value={status}
                                label={'Status'}
                                options={sale_status}
                                onChange={(e) => set_status(e.target.value)}
                                error={errors.status}
                            /> */}
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
                                                                item.discount_percent_2
                                                            }
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'discount_percent_2',
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
                                                                item.discount_percent_1
                                                            }
                                                            onChange={({
                                                                target: {
                                                                    value,
                                                                },
                                                            }) =>
                                                                handleChangeItem(
                                                                    item,
                                                                    'discount_percent_1',
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
                        <div className="w-full flex flex-col justify-between p-4 font-bold text-xl border border-gray-400 rounded-xl">
                            <Checkbox
                                label="Menggunakan PPN"
                                value={use_ppn}
                                onChange={handleSetPpn}
                            />
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
