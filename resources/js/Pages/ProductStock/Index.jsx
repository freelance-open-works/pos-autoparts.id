import React, { useEffect, useState } from 'react'
import { router, Head } from '@inertiajs/react'
import { usePrevious } from 'react-use'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Pagination from '@/Components/DaisyUI/Pagination'
import SearchInput from '@/Components/DaisyUI/SearchInput'
import Card from '@/Components/DaisyUI/Card'
import { formatIDR } from '@/utils'
import { useModalState } from '@/hooks'
import Button from '@/Components/DaisyUI/Button'
import HasPermission from '@/Components/Common/HasPermission'
import { HiPencil } from 'react-icons/hi2'
import FormModal from './FormModal'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const formModal = useModalState()

    const toggleFormModal = (product = null) => {
        formModal.setData(product)
        formModal.toggle()
    }

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

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
        <AuthenticatedLayout page={'System'} action={'Stok'}>
            <Head title="Stok" />

            <div>
                <Card>
                    <div className="flex justify-end mb-4">
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
                                    <th>Harga Jual</th>
                                    <th>Stok</th>
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
                                        <td>{formatIDR(product.price)}</td>
                                        <td>{formatIDR(product.stock)}</td>
                                        <td>
                                            <HasPermission p="edit-product-stock">
                                                <Button
                                                    onClick={() =>
                                                        toggleFormModal(product)
                                                    }
                                                >
                                                    <div className="flex space-x-1 items-center">
                                                        <HiPencil />
                                                        <div>Ubah</div>
                                                    </div>
                                                </Button>
                                            </HasPermission>
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
            <FormModal modalState={formModal} />
        </AuthenticatedLayout>
    )
}
