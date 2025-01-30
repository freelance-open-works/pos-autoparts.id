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
            stock: '',
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
            put(route('product-stocks.update', product), {
                onSuccess: () => handleClose(),
            })
            return
        }
        post(route('product-stocks.update'), {
            onSuccess: () => handleClose(),
        })
    }

    useEffect(() => {
        const product = modalState.data
        if (isEmpty(product) === false) {
            setData({
                stock: product.stock,
            })
            return
        }
    }, [modalState])

    return (
        <Modal isOpen={modalState.isOpen} onClose={handleClose} title={'Stok'}>
            <div className="form-control space-y-2.5">
                <TextInput
                    type="number"
                    name="stock"
                    value={data.stock}
                    onChange={handleOnChange}
                    label="Stock"
                    error={errors.stock}
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
