import { usePage } from '@inertiajs/react'
import { isEmpty } from 'lodash'
import React from 'react'

export default function ApplicationLogo({ className }) {
    const {
        props: {
            app: { app_name, app_logo },
        },
    } = usePage()

    return (
        <>
            {isEmpty(app_logo) && (
                <img src={app_logo} className="w-36 h-3w-36" />
            )}
            <h1 className={className}>{app_name}</h1>
        </>
    )
}
