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
import {
    HiEye,
    HiPaperAirplane,
    HiReceiptPercent,
    HiTruck,
} from 'react-icons/hi2'
import { sale_status_submit } from '@/consts'
import FormModal from './FormModal'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()
    const deliveryModal = useModalState()

    const handleDeleteClick = (sale) => {
        confirmModal.setData(sale)
        confirmModal.toggle()
    }

    const handleDeliveryPrintClick = (sale) => {
        deliveryModal.setData(sale)
        deliveryModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('sales.destroy', confirmModal.data.id))
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
        <AuthenticatedLayout page={'System'} action={'Sale'}>
            <Head title="Sale" />

            <div>
                <Card>
                    <div className="flex justify-between">
                        <HasPermission p="create-sale">
                            <Link href={route('sales.create')}>
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
                                    <th>Invoice Number</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Total (Net)</th>
                                    <th>Status</th>
                                    <th />
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>
                                            {
                                                item.purchase?.purchase_order
                                                    .po_code
                                            }
                                        </td>
                                        <td>{item.s_code}</td>
                                        <td>{item.s_date}</td>
                                        <td>{item.customer.name}</td>
                                        <td>{formatIDR(item.amount_cost)}</td>
                                        <td>{item.status}</td>
                                        <td className="text-right">
                                            {item.status ===
                                                sale_status_submit && (
                                                <Dropdown label={'Cetak'}>
                                                    <a
                                                        href={route(
                                                            'sales.print',
                                                            item
                                                        )}
                                                        target="_blank"
                                                    >
                                                        <Dropdown.Item>
                                                            <div className="flex space-x-1 items-center">
                                                                <HiReceiptPercent />
                                                                <div>
                                                                    Invoice
                                                                </div>
                                                            </div>
                                                        </Dropdown.Item>
                                                    </a>
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            handleDeliveryPrintClick(
                                                                item
                                                            )
                                                        }
                                                    >
                                                        <div className="flex space-x-1 items-center">
                                                            <HiTruck />
                                                            <div>
                                                                Surat Jalan +
                                                                Label
                                                            </div>
                                                        </div>
                                                    </Dropdown.Item>
                                                </Dropdown>
                                            )}
                                        </td>
                                        <td className="text-right">
                                            <Dropdown label={'Opsi'}>
                                                <Dropdown.Item>
                                                    <Link
                                                        href={route(
                                                            'sales.show',
                                                            item
                                                        )}
                                                        className="flex space-x-1 items-center"
                                                    >
                                                        <HiEye />
                                                        <div>View</div>
                                                    </Link>
                                                </Dropdown.Item>
                                                {item.allow_change === true && (
                                                    <>
                                                        <HasPermission p="update-sale">
                                                            <>
                                                                <Dropdown.Item>
                                                                    <Link
                                                                        href={route(
                                                                            'sales.patch',
                                                                            item
                                                                        )}
                                                                        method="patch"
                                                                        data={{
                                                                            key: 'status',
                                                                            value: sale_status_submit,
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
                                                                                'sales.edit',
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
                                                        <HasPermission p="delete-sale">
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
            <FormModal modalState={deliveryModal} />
        </AuthenticatedLayout>
    )
}
