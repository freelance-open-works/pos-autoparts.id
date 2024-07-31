import React, { useEffect } from 'react'
import { router, useForm } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import Modal from '@/Components/DaisyUI/Modal'
import Button from '@/Components/DaisyUI/Button'
import TextInput from '@/Components/DaisyUI/TextInput'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import FormInputDate from '@/Components/DaisyUI/FormInputDate'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'
import { delivery_services } from '@/consts'
import { openInNewTab } from '@/utils'

export default function FormModal(props) {
    const { modalState } = props
    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            sale: null,
            expedition: null,
            expedition_id: '',
            sd_date: '',
            qty: '',
            qty_unit: '',
            volume: '',
            volume_unit: '',
            note: '',
            service: '',
        })

    const handleOnChange = (event) => {
        setData(
            event.target.name,
            event.target.type === 'checkbox'
                ? event.target.checked
                    ? 1
                    : 0
                : event.target.value
        )
    }

    const handleReset = () => {
        modalState.setData(null)
        reset()
        clearErrors()
    }

    const handleClose = () => {
        handleReset()
        modalState.toggle()
    }

    const handleSubmit = () => {
        post(route('sales.delivery', data.sale.id), {
            onSuccess: () => {
                handleClose()
                openInNewTab(route('sales.delivery-print', data.sale.id))
            },
        })
    }

    useEffect(() => {
        const sale = modalState.data
        if (isEmpty(sale) === false) {
            setData({
                sale: sale,
                expedition: isEmpty(sale.delivery?.expedition)
                    ? {}
                    : sale.delivery?.expedition,
                expedition_id: sale.delivery?.expedition_id ?? '',
                sd_code: sale.delivery?.sd_code ?? '',
                sd_date: isEmpty(sale.delivery?.sd_date)
                    ? new Date()
                    : sale.delivery.sd_date,
                qty: sale.delivery?.qty ?? '',
                qty_unit: sale.delivery?.qty_unit ?? '',
                volume: sale.delivery?.volume ?? '',
                volume_unit: sale.delivery?.volume_unit ?? '',
                note: sale.delivery?.note ?? '',
                service: sale.delivery?.service ?? '',
            })
            return
        }
    }, [modalState])

    return (
        <Modal
            isOpen={modalState.isOpen}
            onClose={handleClose}
            title={'Cetak Surat Jalan + Label'}
            maxW="md:max-w-xl"
        >
            <div className="form-control space-y-2.5">
                <TextInput
                    placeholder="autogenerate"
                    value={data.sd_code}
                    label="No. Surat Jalan"
                    readOnly={true}
                />
                <FormInputDate
                    value={data.sd_date}
                    label={'Tanggal'}
                    onChange={(date) => setData('sd_date', date)}
                    error={errors.sd_date}
                />
                <SelectModalInput
                    label="Ekspedisi"
                    value={data.expedition}
                    onChange={(item) =>
                        setData({
                            ...data,
                            expedition: item,
                            expedition_id: item ? item.id : null,
                        })
                    }
                    error={errors.expedition_id}
                    params={{
                        table: 'expeditions',
                        columns: 'id|name|address',
                        orderby: 'created_at.asc',
                    }}
                />
                <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-2">
                    <TextInput
                        name="qty"
                        value={data.qty}
                        label="Qty"
                        onChange={handleOnChange}
                        error={errors.qty}
                    />
                    <TextInput
                        name="qty_unit"
                        value={data.qty_unit}
                        label="Satuan Qty"
                        onChange={handleOnChange}
                        error={errors.qty_unit}
                    />
                    <TextInput
                        name="volume"
                        value={data.volume}
                        label="Volume"
                        onChange={handleOnChange}
                        error={errors.volume}
                    />
                    <TextInput
                        name="volume_unit"
                        value={data.volume_unit}
                        label="Satuan Volume"
                        onChange={handleOnChange}
                        error={errors.volume_unit}
                    />
                </div>
                <SelectOptionArray
                    name="service"
                    value={data.service}
                    label="Layanan"
                    options={delivery_services}
                    onChange={handleOnChange}
                />
                <TextareaInput
                    name="note"
                    value={data.note}
                    label="Keterangan"
                    onChange={handleOnChange}
                    error={errors.note}
                />
                <div className="flex items-center space-x-2 mt-4">
                    <Button
                        onClick={handleSubmit}
                        processing={processing}
                        type="primary"
                    >
                        Cetak
                    </Button>
                    <Button onClick={handleClose} type="secondary">
                        Batal
                    </Button>
                </div>
            </div>
        </Modal>
    )
}
