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
import { formatDate } from '@/utils'
import { HiEye, HiPaperAirplane } from 'react-icons/hi2'
import { claim_status_submit } from '@/consts'

export default function Index(props) {
    const {
        data: { links, data },
    } = props

    const [search, setSearch] = useState('')
    const preValue = usePrevious(search)

    const confirmModal = useModalState()

    const handleDeleteClick = (claim) => {
        confirmModal.setData(claim)
        confirmModal.toggle()
    }

    const onDelete = () => {
        if (confirmModal.data !== null) {
            router.delete(route('claims.destroy', confirmModal.data.id))
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
        <AuthenticatedLayout page={'System'} action={'Claim'}>
            <Head title="Claim" />

            <div>
                <Card>
                    <div className="flex justify-between">
                        <HasPermission p="create-claim">
                            <Link href={route('claims.create')}>
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
                        <table className="table mt-14">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Customer</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((claim, index) => (
                                    <tr key={claim.id}>
                                        <td>{claim.sale.s_code}</td>
                                        <td>{claim.customer.name}</td>
                                        <td>{formatDate(claim.c_date)}</td>
                                        <td>{claim.status}</td>
                                        <td className="text-right">
                                            <Dropdown label={'Opsi'}>
                                                <Dropdown.Item
                                                    onClick={() =>
                                                        router.visit(
                                                            route(
                                                                'claims.show',
                                                                claim
                                                            )
                                                        )
                                                    }
                                                >
                                                    <div className="flex space-x-1 items-center">
                                                        <HiEye />
                                                        <div>View</div>
                                                    </div>
                                                </Dropdown.Item>
                                                <HasPermission p="update-claim">
                                                    <>
                                                        <Dropdown.Item>
                                                            <Link
                                                                href={route(
                                                                    'claims.patch',
                                                                    claim.id
                                                                )}
                                                                method="patch"
                                                                data={{
                                                                    key: 'status',
                                                                    value: claim_status_submit,
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
                                                                        'claims.edit',
                                                                        claim
                                                                    )
                                                                )
                                                            }
                                                        >
                                                            <div className="flex space-x-1 items-center">
                                                                <HiPencil />
                                                                <div>Ubah</div>
                                                            </div>
                                                        </Dropdown.Item>
                                                    </>
                                                </HasPermission>
                                                <HasPermission p="delete-claim">
                                                    <Dropdown.Item
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                claim
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
            <ModalConfirm modalState={confirmModal} onConfirm={onDelete} />
        </AuthenticatedLayout>
    )
}
