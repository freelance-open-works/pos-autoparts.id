import React, { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { usePrevious } from 'react-use'
import { Head, Link } from '@inertiajs/react'
import { HiPencil, HiTrash } from 'react-icons/hi'
import { HiEye, HiPaperAirplane } from 'react-icons/hi2'
import { useModalState } from '@/hooks'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import Pagination from '@/Components/DaisyUI/Pagination'
import ModalConfirm from '@/Components/DaisyUI/ModalConfirm'
import SearchInput from '@/Components/DaisyUI/SearchInput'
import HasPermission from '@/Components/Common/HasPermission'
import Dropdown from '@/Components/DaisyUI/Dropdown'
import Button from '@/Components/DaisyUI/Button'
import Card from '@/Components/DaisyUI/Card'
import { formatIDR, hasPermissionAuth } from '@/utils'
import { purchase_order_status_submit } from '@/consts'
import Visible from '@/Components/Common/Visible'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()

    const handleDeleteClick = (purchase) => {
        confirmModal.setData(purchase)
        confirmModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('purchases.destroy', confirmModal.data.id))
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
        <AuthenticatedLayout page={'System'} action={'Purchase'}>
            <Head title="Purchase" />

            <div>
                <Card>
                    <div className="flex justify-between">
                        <HasPermission p="create-purchase">
                            <Link href={route('purchases.create')}>
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
                                    <th>No PO</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Total (Net)</th>
                                    <th>Status</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{item.purchase_order?.po_code}</td>
                                        <td>{item.p_date}</td>
                                        <td>{item.supplier.name}</td>
                                        <td>{formatIDR(item.amount_cost)}</td>
                                        <td>{item.status}</td>
                                        <td className="text-right">
                                            <div className="w-full flex flex-row gap-2">
                                                <a
                                                    href={route(
                                                        'purchases.print',
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
                                                                'purchases.show',
                                                                item
                                                            )}
                                                            className="flex space-x-1 items-center"
                                                        >
                                                            <HiEye />
                                                            <div>View</div>
                                                        </Link>
                                                    </Dropdown.Item>
                                                    <HasPermission p="update-purchase">
                                                        <Visible
                                                            v={
                                                                item.allow_change
                                                            }
                                                        >
                                                            <Dropdown.Item>
                                                                <Link
                                                                    href={route(
                                                                        'purchases.patch',
                                                                        item
                                                                    )}
                                                                    method="patch"
                                                                    data={{
                                                                        key: 'status',
                                                                        value: purchase_order_status_submit,
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
                                                        </Visible>
                                                        <Visible
                                                            v={
                                                                item.allow_change ||
                                                                hasPermissionAuth(
                                                                    'force-update-purchase'
                                                                )
                                                            }
                                                        >
                                                            <Dropdown.Item
                                                                onClick={() =>
                                                                    router.visit(
                                                                        route(
                                                                            'purchases.edit',
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
                                                        </Visible>
                                                    </HasPermission>
                                                    <Visible
                                                        v={item.allow_change}
                                                    >
                                                        <HasPermission p="delete-purchase">
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
                                                    </Visible>
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
