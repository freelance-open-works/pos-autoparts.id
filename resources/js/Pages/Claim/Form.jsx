import React, { useEffect, useState } from 'react'
import { router, Head, Link, usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import SelectModalSale from '../Sale/SelectModal'
import FormInputDate from '@/Components/DaisyUI/FormInputDate'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import { claim_status, claim_status_draft } from '@/consts'
import { HiXMark } from 'react-icons/hi2'

export default function Form(props) {
    const {
        props: { errors },
    } = usePage()
    const { claim } = props

    const [processing, setProcessing] = useState(false)

    const [sale, set_sale] = useState(null)
    const [customer, set_customer] = useState(null)
    const [c_date, set_c_date] = useState(new Date())
    const [reason, set_reason] = useState('')
    const [status, set_status] = useState(claim_status_draft)
    const [items, set_items] = useState([])

    const handleSetSale = (sale) => {
        set_sale(sale)
        set_customer(sale.customer)
        set_items(
            sale.items.map((item) => {
                return {
                    ...item.product,
                    product_id: item.product_id,
                    sale_item_id: item.id,
                    price: item.price,
                    qty_item: item.qty,
                    qty_return: item.qty_return,
                    qty: 0,
                }
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
                        if (value < 1) {
                            return i
                        }
                    }
                    i[name] = value
                }
                return i
            })
        )
    }

    const payload = {
        sale_id: sale?.id,
        customer_id: customer?.id,
        c_date,
        reason,
        status,
        items,
    }

    const handleSubmit = () => {
        if (isEmpty(claim) === false) {
            router.put(route('claims.update', claim), payload, {
                onStart: () => setProcessing(true),
                onFinish: (e) => {
                    setProcessing(false)
                },
            })
            return
        }
        router.post(route('claims.store'), payload, {
            onStart: () => setProcessing(true),
            onFinish: (e) => {
                setProcessing(false)
            },
        })
    }

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
                        <SelectModalSale
                            label="Penjualan"
                            placeholder="Pilih Penjualan"
                            onChange={handleSetSale}
                            error={errors.sale_id}
                            value={sale?.s_code}
                        />
                        <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2 border border-gray-400 rounded-xl px-2 py-4">
                            <FormInputDate
                                value={c_date}
                                label={'Tanggal'}
                                onChange={(date) => set_c_date(date)}
                                error={errors.c_date}
                            />
                            <TextInput
                                placeholder="Customer"
                                value={customer?.name}
                                label="Customer"
                                readOnly={true}
                            />
                            {/* <SelectOptionArray
                                value={status}
                                label={'Status'}
                                options={claim_status}
                                onChange={(e) => set_status(e.target.value)}
                                error={errors.status}
                            /> */}
                            <TextInput
                                name="reason"
                                value={reason}
                                onChange={(e) => set_reason(e.target.value)}
                                label="Alasan Pengambalian"
                                error={errors.reason}
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
                                            <th></th>
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

                        <div className="flex items-center">
                            <div className="flex space-x-2">
                                <Button
                                    onClick={handleSubmit}
                                    processing={processing}
                                    type="primary"
                                >
                                    Simpan
                                </Button>
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
