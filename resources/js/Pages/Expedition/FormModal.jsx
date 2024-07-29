import React, { useEffect } from 'react'
import { useForm } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import Modal from '@/Components/DaisyUI/Modal'
import Button from '@/Components/DaisyUI/Button'
import TextInput from '@/Components/DaisyUI/TextInput'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'

export default function FormModal(props) {
    const { modalState } = props
    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: '',
            address: '',
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
        const expedition = modalState.data
        if (expedition !== null) {
            put(route('expeditions.update', expedition), {
                onSuccess: () => handleClose(),
            })
            return
        }
        post(route('expeditions.store'), {
            onSuccess: () => handleClose(),
        })
    }

    useEffect(() => {
        const expedition = modalState.data
        if (isEmpty(expedition) === false) {
            setData({
                name: expedition.name,
                address: expedition.address,
            })
            return
        }
    }, [modalState])

    return (
        <Modal
            isOpen={modalState.isOpen}
            onClose={handleClose}
            title={'Ekpedisi'}
        >
            <div className="form-control space-y-2.5">
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
