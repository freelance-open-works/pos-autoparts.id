import React, { useEffect } from 'react'
import { useForm } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import Modal from '@/Components/DaisyUI/Modal'
import Button from '@/Components/DaisyUI/Button'
import TextInput from '@/Components/DaisyUI/TextInput'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'
import { SelectOptionArray } from '@/Components/DaisyUI/SelectInput'

export default function FormModal(props) {
    const { modalState } = props
    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: '',
            code: '',
            address: '',
            type: '',
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
        const customer = modalState.data
        if (customer !== null) {
            put(route('customers.update', customer), {
                onSuccess: () => handleClose(),
            })
            return
        }
        post(route('customers.store'), {
            onSuccess: () => handleClose(),
        })
    }

    useEffect(() => {
        const customer = modalState.data
        if (isEmpty(customer) === false) {
            setData({
                name: customer.name,
                code: customer.code,
                address: customer.address,
                type: customer.type,
            })
            return
        }
    }, [modalState])

    return (
        <Modal
            isOpen={modalState.isOpen}
            onClose={handleClose}
            title={'Customer'}
        >
            <div className="form-control space-y-2.5">
                <TextInput
                    name="code"
                    value={data.code}
                    onChange={handleOnChange}
                    label="Kode"
                    error={errors.code}
                />
                <TextInput
                    name="name"
                    value={data.name}
                    onChange={handleOnChange}
                    label="Name"
                    error={errors.name}
                />
                <TextareaInput
                    name="address"
                    value={data.address}
                    onChange={handleOnChange}
                    label="Alamat"
                    error={errors.address}
                />
                <SelectOptionArray
                    name="type"
                    value={data.type}
                    label="Tipe"
                    options={['incity', 'outcity']}
                    onChange={handleOnChange}
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
