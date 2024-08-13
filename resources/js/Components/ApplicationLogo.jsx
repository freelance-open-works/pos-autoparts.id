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
            {!isEmpty(app_logo) && (
                <img
                    src={route('file.show', app_logo)}
                    className="h-30 px-4 py-6"
                />
            )}
        </>
    )
}
