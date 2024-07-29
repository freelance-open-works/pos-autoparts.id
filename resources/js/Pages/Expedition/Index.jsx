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

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()
    const formModal = useModalState()

    const toggleFormModal = (expedition = null) => {
        formModal.setData(expedition)
        formModal.toggle()
    }

    const handleDeleteClick = (expedition) => {
        confirmModal.setData(expedition)
        confirmModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('expeditions.destroy', confirmModal.data.id))
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
        <AuthenticatedLayout page={'System'} action={'Ekpedisi'}>
            <Head title="Ekpedisi" />

            <div>
                <Card>
                    <div className="flex justify-between mb-4">
                        <HasPermission p="create-expedition">
                            <Button
                                size="sm"
                                onClick={() => toggleFormModal()}
                                type="primary"
                            >
                                Tambah
                            </Button>
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
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((expedition, index) => (
                                    <tr key={expedition.id}>
                                        <td>{expedition.name}</td>
                                        <td>{expedition.address}</td>
                                        <td className="text-end">
                                            <Dropdown label={'Opsi'}>
                                                <HasPermission p="update-expedition">
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            toggleFormModal(
                                                                expedition
                                                            )
                                                        }
                                                    >
                                                        <div className="flex space-x-1 items-center">
                                                            <HiPencil />
                                                            <div>Ubah</div>
                                                        </div>
                                                    </Dropdown.Item>
                                                </HasPermission>
                                                <HasPermission p="delete-expedition">
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                expedition
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
        </AuthenticatedLayout>
    )
}
