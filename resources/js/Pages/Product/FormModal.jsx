import React, { useEffect } from 'react'
import { useForm } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import Modal from '@/Components/DaisyUI/Modal'
import Button from '@/Components/DaisyUI/Button'
import TextInput from '@/Components/DaisyUI/TextInput'
import SelectModalInput from '@/Components/DaisyUI/SelectModalInput'

export default function FormModal(props) {
    const { modalState } = props
    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: '',
            part_code: '',
            type: '',
            discount: '',
            cost: '',
            price: '',
            brand_id: '',
            brand: '',
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
        const product = modalState.data
        if (product !== null) {
            put(route('products.update', product), {
                onSuccess: () => handleClose(),
            })
            return
        }
        post(route('products.store'), {
            onSuccess: () => handleClose(),
        })
    }

    useEffect(() => {
        const product = modalState.data
        if (isEmpty(product) === false) {
            setData({
                name: product.name,
                part_code: product.part_code,
                type: product.type,
                discount: product.discount,
                cost: product.cost,
                price: product.price,
                brand_id: product.brand_id,
                brand: product.brand,
            })
            return
        }
    }, [modalState])

    return (
        <Modal
            isOpen={modalState.isOpen}
            onClose={handleClose}
            title={'Product'}
        >
            <div className="form-control space-y-2.5">
                <TextInput
                    name="part_code"
                    value={data.part_code}
                    onChange={handleOnChange}
                    label="Part No"
                    error={errors.part_code}
                />
                <TextInput
                    name="name"
                    value={data.name}
                    onChange={handleOnChange}
                    label="Name"
                    error={errors.name}
                />
                <TextInput
                    name="type"
                    value={data.type}
                    onChange={handleOnChange}
                    label="Tipe"
                    error={errors.type}
                />
                <TextInput
                    name="discount"
                    value={data.discount}
                    onChange={handleOnChange}
                    label="Diskon (%)"
                    error={errors.discount}
                />
                <TextInput
                    name="cost"
                    value={data.cost}
                    onChange={handleOnChange}
                    label="Harga Beli"
                    error={errors.cost}
                />
                <TextInput
                    name="price"
                    value={data.price}
                    onChange={handleOnChange}
                    label="Harga Jual"
                    error={errors.price}
                />
                <SelectModalInput
                    label="Merk"
                    value={data.brand}
                    onChange={(item) =>
                        setData({
                            ...data,
                            brand: item,
                            brand_id: item ? item.id : null,
                        })
                    }
                    error={errors.brand_id}
                    params={{
                        table: 'brands',
                        columns: 'id|name',
                        orderby: 'created_at.asc',
                    }}
                />
                <div className="flex items-center space-x-2 mt-4">
                    <Button
                        onClick={handleSubmit}
                        processing={processing}
                        type="primary"
                    >
                        Simpan
                    </Button>
                    <Button onClick={handleClose} type="secondary">
                        Batal
                    </Button>
                </div>
            </div>
        </Modal>
    )
}
