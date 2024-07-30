import React from 'react'
import { Head, useForm } from '@inertiajs/react'
import { isEmpty } from 'lodash'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Card from '@/Components/DaisyUI/Card'
import TextInput from '@/Components/DaisyUI/TextInput'
import Button from '@/Components/DaisyUI/Button'
import FormFile from '@/Components/DaisyUI/FormFile'
import TextareaInput from '@/Components/DaisyUI/TextareaInput'

const extractValue = (set, key) => {
    const find = set.find((s) => s.key === key)
    if (isEmpty(find) === false) {
        if (find.type === 'image') {
            return find?.url
        }
        return find?.value
    }
    return ''
}

export default function Setting(props) {
    const { setting } = props

    const app_logo_url = extractValue(setting, 'app_logo')
    const { data, setData, post, processing, errors } = useForm({
        app_name: extractValue(setting, 'app_name'),
        app_logo: '',
        company_name: extractValue(setting, 'company_name'),
        company_address: extractValue(setting, 'company_address'),
        ppn_percent: extractValue(setting, 'ppn_percent'),
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

    const handleSubmit = () => {
        post(route('setting.update'))
    }

    return (
        <AuthenticatedLayout page={'System'} action={'Setting'}>
            <Head title="Setting" />

            <div>
                <Card>
                    <div className="text-xl font-bold mb-4 text-base-content">
                        Setting
                    </div>
                    <TextInput
                        name="app_name"
                        value={data.app_name}
                        onChange={handleOnChange}
                        label="App Title"
                        error={errors.app_name}
                    />
                    <FormFile
                        label={'App Logo'}
                        onChange={(file_path) => setData('app_logo', file_path)}
                        error={errors.app_logo}
                        url={app_logo_url}
                        filemimes="image/jpg,image/jpeg,image/png"
                    />
                    <TextInput
                        name="company_name"
                        value={data.company_name}
                        onChange={handleOnChange}
                        label="Perusahaan"
                        error={errors.company_name}
                    />
                    <TextareaInput
                        name="company_address"
                        value={data.company_address}
                        onChange={handleOnChange}
                        label="Alamat"
                        error={errors.company_address}
                    />
                    <TextInput
                        name="ppn_percent"
                        value={data.ppn_percent}
                        onChange={handleOnChange}
                        label="PPN (%)"
                        error={errors.ppn_percent}
                    />
                    <div className="mt-4">
                        <Button
                            onClick={handleSubmit}
                            processing={processing}
                            type="primary"
                        >
                            Simpan
                        </Button>
                    </div>
                </Card>
            </div>
        </AuthenticatedLayout>
    )
}
