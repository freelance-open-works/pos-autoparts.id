import moment from 'moment'
import { toast } from 'sonner'
import { isEmpty } from 'lodash'
import { usePage } from '@inertiajs/react'

export const formatDate = (date) => {
    if (isEmpty(date)) {
        return ''
    }
    return moment(date).format('DD/MM/yyyy')
}

export const formatDateTime = (date) => {
    if (isEmpty(date)) {
        return ''
    }
    return moment(date).format('DD/MM/yyyy HH:mm:ss')
}

export const dateToString = (date) => {
    return moment(date).format('MM/DD/yyyy')
}

export const converToDate = (date) => {
    if (isEmpty(date) == false) {
        return new Date(date)
    }

    return ''
}

export function formatIDR(amount) {
    const idFormatter = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    })
    return idFormatter.format(amount)
}

export const formatIDDate = (date) => {
    const month = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ]
    date = new Date(date)

    return `${date.getDate()} ${month[date.getMonth()]} ${date.getFullYear()}`
}

export const hasPermissionAuth = (permission) => {
    const {
        props: { auth },
    } = usePage()

    return hasPermission(auth, permission)
}

export const hasPermission = (auth, permission) => {
    const { user } = auth
    if (user.role === null) {
        return true
    }

    let has = user.role.permissions.find((item) => item.name === permission)

    if (has) {
        return true
    }
    return false
}

export const showToast = (message, type) => {
    if (type === 'success') {
        toast.success(message)
        return
    }
    if (type === 'error') {
        toast.error(message)
        return
    }
    toast(message)
}

export const openInNewTab = (href) => {
    Object.assign(document.createElement('a'), {
        target: '_blank',
        rel: 'noopener noreferrer',
        href: href,
    }).click()
}
