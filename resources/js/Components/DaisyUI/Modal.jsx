import React from 'react'
import { HiX } from 'react-icons/hi'

export default function Modal({
    children,
    title = '',
    isOpen,
    onClose,
    maxW = 'md:max-w-md',
}) {
    return (
        <dialog
            className={`w-full modal modal-bottom md:modal-middle ${
                isOpen ? 'modal-open' : ''
            }`}
        >
            <div className={`modal-box md:w-11/12 ${maxW}`}>
                {title && <h3 className="font-bold text-lg">{title}</h3>}
                <form method="dialog">
                    <button
                        className="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                        onClick={onClose}
                    >
                        <HiX className="h-4 w-4" />
                    </button>
                </form>
                <div className="w-full">{children}</div>
            </div>
        </dialog>
    )
}
