import React from 'react'
import { Head } from '@inertiajs/react'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import DummyDashbord from '@/Components/Dummy/Dashboard'
import { formatIDR } from '@/utils'

export default function Dashboard(props) {
    return (
        <AuthenticatedLayout page={'Dashboard'} action={''}>
            <Head title="Dashboard" />

            <div>
                <div className="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 py-2 gap-2">
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Roles</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.role_count)}{' '}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Users</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.user_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Customer</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.customer_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Supplier</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.supplier_count)}
                            </div>
                        </div>
                    </div>
                    <div className="stats shadow flex-1">
                        <div className="stat">
                            <div className="stat-title">Product</div>
                            <div className="stat-value text-primary">
                                {formatIDR(props.product_count)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
