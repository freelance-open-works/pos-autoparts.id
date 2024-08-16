import React, { useEffect, useState } from 'react'
import { router, Head } from '@inertiajs/react'
import { usePrevious } from 'react-use'
import { HiPencil, HiTrash } from 'react-icons/hi'
import { useModalState } from '@/hooks'

import HasPermission from '@/Components/Common/HasPermission'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Pagination from '@/Components/DaisyUI/Pagination'
import ModalConfirm from '@/Components/DaisyUI/ModalConfirm'
import SearchInput from '@/Components/DaisyUI/SearchInput'
import Button from '@/Components/DaisyUI/Button'
import Dropdown from '@/Components/DaisyUI/Dropdown'
import Card from '@/Components/DaisyUI/Card'
import FormModal from './FormModal'
import { formatIDR } from '@/utils'
import ImportModal from './ImportModal'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()
    const formModal = useModalState()
    const importModal = useModalState()

    const toggleFormModal = (product = null) => {
        formModal.setData(product)
        formModal.toggle()
    }

    const handleDeleteClick = (product) => {
        confirmModal.setData(product)
        confirmModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('products.destroy', confirmModal.data.id))
        }
    }

    const params = { q: search }
    useEffect(() => {
        if (preValue) {
            router.get(
                route(route().current()),
                { q: search },
                {
                    replace: true,
                    preserveState: true,
                }
            )
        }
    }, [search])

    return (
        <AuthenticatedLayout page={'System'} action={'Product'}>
            <Head title=" Product" />

            <div>
                <Card>
                    <div className="flex justify-between mb-4">
                        <HasPermission p="create-product">
                            <div className="grid grid-cols-2 gap-2">
                                <Button
                                    size="sm"
                                    onClick={() => toggleFormModal()}
                                    type="primary"
                                >
                                    Tambah
                                </Button>
                                <Button
                                    size="sm"
                                    onClick={importModal.toggle}
                                    type="secondary"
                                >
                                    Import
                                </Button>
                            </div>
                        </HasPermission>
                        <div className="flex items-center">
                            <SearchInput
                                onChange={(e) => setSearch(e.target.value)}
                                value={search}
                            />
                        </div>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="table mb-4">
                            <thead>
                                <tr>
                                    <th>Part No</th>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Merk</th>
                                    <th>Diskon (%)</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((product, index) => (
                                    <tr key={product.id}>
                                        <td>{product.part_code}</td>
                                        <td>{product.name}</td>
                                        <td>{product.type}</td>
                                        <td>{product.brand.name}</td>
                                        <td>{formatIDR(product.discount)}</td>
                                        <td>{formatIDR(product.cost)}</td>
                                        <td>{formatIDR(product.price)}</td>
                                        <td className="text-end">
                                            <Dropdown label={'Opsi'}>
                                                <HasPermission p="update-product">
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            toggleFormModal(
                                                                product
                                                            )
                                                        }
                                                    >
                                                        <div className="flex space-x-1 items-center">
                                                            <HiPencil />
                                                            <div>Ubah</div>
                                                        </div>
                                                    </Dropdown.Item>
                                                </HasPermission>
                                                <HasPermission p="delete-product">
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                product
                                                            )
                                                        }
                                                    >
                                                        <div className="flex space-x-1 items-center">
                                                            <HiTrash />
                                                            <div>Hapus</div>
                                                        </div>
                                                    </Dropdown.Item>
                                                </HasPermission>
                                            </Dropdown>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div className="w-full overflow-x-auto flex lg:justify-center">
                        <Pagination links={links} params={params} />
                    </div>
                </Card>
            </div>
            <ModalConfirm onConfirm={onDelete} modalState={confirmModal} />
            <FormModal modalState={formModal} />
            <ImportModal modalState={importModal} />
        </AuthenticatedLayout>
    )
}
