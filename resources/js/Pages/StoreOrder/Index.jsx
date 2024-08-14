import React, { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { usePrevious } from 'react-use'
import { Head, Link } from '@inertiajs/react'
import { HiPencil, HiTrash } from 'react-icons/hi'
import { useModalState } from '@/hooks'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Pagination from '@/Components/DaisyUI/Pagination'
import ModalConfirm from '@/Components/DaisyUI/ModalConfirm'
import SearchInput from '@/Components/DaisyUI/SearchInput'
import HasPermission from '@/Components/Common/HasPermission'
import Dropdown from '@/Components/DaisyUI/Dropdown'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import { formatIDR } from '@/utils'
import { HiEye, HiPaperAirplane } from 'react-icons/hi2'
import { store_order_status_submit } from '@/consts'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()

    const handleDeleteClick = (storeOrder) => {
        confirmModal.setData(storeOrder)
        confirmModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('store-orders.destroy', confirmModal.data.id))
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
        <AuthenticatedLayout page={'System'} action={'Order Toko'}>
            <Head title="Order Toko" />

            <div>
                <Card>
                    <div className="flex justify-between">
                        <HasPermission p="create-store-order">
                            <Link href={route('store-orders.create')}>
                                <Button size="sm" type="primary">
                                    Tambah
                                </Button>
                            </Link>
                        </HasPermission>

                        <div className="flex items-center">
                            <SearchInput
                                onChange={(e) => setSearch(e.target.value)}
                                value={search}
                            />
                        </div>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="table mt-12">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kode Customer</th>
                                    <th>Customer</th>
                                    {/* <th>Total</th> */}
                                    <th>Status</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{item.so_code}</td>
                                        <td>{item.so_date}</td>
                                        <td>{item.customer.code}</td>
                                        <td>{item.customer.name}</td>
                                        {/* <td>{formatIDR(item.amount_cost)}</td> */}
                                        <td>{item.status}</td>
                                        <td className="text-right">
                                            <div className="w-full flex flex-row gap-2">
                                                <a
                                                    href={route(
                                                        'store-orders.print',
                                                        item
                                                    )}
                                                    target="_blank"
                                                >
                                                    <Button>Cetak</Button>
                                                </a>
                                                <Dropdown label={'Opsi'}>
                                                    <Dropdown.Item>
                                                        <Link
                                                            href={route(
                                                                'store-orders.show',
                                                                item
                                                            )}
                                                            className="flex space-x-1 items-center"
                                                        >
                                                            <HiEye />
                                                            <div>View</div>
                                                        </Link>
                                                    </Dropdown.Item>
                                                    {item.allow_change ===
                                                        true && (
                                                        <>
                                                            <HasPermission p="update-store-order">
                                                                <>
                                                                    <Dropdown.Item>
                                                                        <Link
                                                                            href={route(
                                                                                'store-orders.patch',
                                                                                item
                                                                            )}
                                                                            method="patch"
                                                                            data={{
                                                                                key: 'status',
                                                                                value: store_order_status_submit,
                                                                            }}
                                                                            className="flex space-x-1 items-center"
                                                                            as="button"
                                                                        >
                                                                            <HiPaperAirplane />
                                                                            <div>
                                                                                Submit
                                                                            </div>
                                                                        </Link>
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item
                                                                        onClick={() =>
                                                                            router.visit(
                                                                                route(
                                                                                    'store-orders.edit',
                                                                                    item
                                                                                )
                                                                            )
                                                                        }
                                                                    >
                                                                        <div className="flex space-x-1 items-center">
                                                                            <HiPencil />
                                                                            <div>
                                                                                Ubah
                                                                            </div>
                                                                        </div>
                                                                    </Dropdown.Item>
                                                                </>
                                                            </HasPermission>
                                                            <HasPermission p="delete-store-order">
                                                                <Dropdown.Item
                                                                    onClick={() =>
                                                                        handleDeleteClick(
                                                                            item
                                                                        )
                                                                    }
                                                                >
                                                                    <div className="flex space-x-1 items-center">
                                                                        <HiTrash />
                                                                        <div>
                                                                            Hapus
                                                                        </div>
                                                                    </div>
                                                                </Dropdown.Item>
                                                            </HasPermission>
                                                        </>
                                                    )}
                                                </Dropdown>
                                            </div>
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
            <ModalConfirm modalState={confirmModal} onConfirm={onDelete} />
        </AuthenticatedLayout>
    )
}
