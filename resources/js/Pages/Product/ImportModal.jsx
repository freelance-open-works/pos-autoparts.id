import React from 'react'
import { useForm } from '@inertiajs/react'

import Modal from '@/Components/DaisyUI/Modal'
import Button from '@/Components/DaisyUI/Button'
import FormFile from '@/Components/DaisyUI/FormFile'

export default function ImportModal(props) {
    const { modalState } = props
    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            products: '',
        })

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
        post(route('product-imports.store'), {
            onSuccess: () => handleClose(),
        })
    }

    return (
        <Modal
            isOpen={modalState.isOpen}
            onClose={handleClose}
            title={'Import Product'}
        >
            <div className="form-control space-y-2.5">
                <FormFile
                    label={'File'}
                    onChange={(file_path) => setData('products', file_path)}
                    error={errors.products}
                />
                <p>
                    contoh format import dapat di download{' '}
                    <a
                        href={route('file.show', 'example-products.csv')}
                        target="_blank"
                        className="text-blue-600 underline"
                    >
                        disini
                    </a>
                </p>
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
