import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

import Modal from '@/Components/DaisyUI/Modal';
import PaginationApi from '@/Components/DaisyUI/PaginationApi';
import Spinner from '@/Components/DaisyUI/Spinner';
import SearchInput from '@/Components/DaisyUI/SearchInput';
import { useDebounce, usePagination } from '@/hooks';
import { formatIDR } from '@/utils';

export default function SelectModalPurchase(props) {
    const {
        props: { auth },
    } = usePage();

    const { label, value, error, onChange, placeholder = '' } = props;

    const [search, setSearch] = useState('');
    const q = useDebounce(search, 750);

    const [isOpen, setOpen] = useState();

    const toggle = () => {
        setSearch('');
        setOpen(!isOpen);
    };

    const [data, fetch, loading] = usePagination(auth, `api.purchases.index`);

    const handleItemSelected = (item) => {
        onChange(item);
        toggle();
    };

    // in state isOpen change
    useEffect(() => {
        if (isOpen === true) {
            fetch(1);
        }
    }, [isOpen]);

    // in searching
    useEffect(() => {
        fetch(1, { q });
    }, [q]);

    return (
        <>
            <div className="form-control">
                <div className="label">
                    <label className="label-text">{label}</label>
                </div>
                <input
                    className={`input input-bordered w-full ${
                        error && 'input-error'
                    }`}
                    value={value}
                    onClick={toggle}
                    placeholder={placeholder}
                    readOnly={true}
                />
                <p className="label-text text-red-600">{error}</p>
            </div>
            <Modal isOpen={isOpen} onClose={toggle} maxW={`md:max-w-4xl`}>
                <div className="mb-3"></div>
                <SearchInput
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
                {loading ? (
                    <div className="w-full flex justify-center items-center gap-4 mt-3 h-36">
                        <Spinner />
                        <div>Loading </div>
                    </div>
                ) : (
                    <>
                        <table className="table mt-3">
                            <thead>
                                <tr>
                                    <th>No PO</th>
                                    <th>Tanggal</th>
                                    <th>Kode Cust</th>
                                    <th>Supplier</th>
                                    <th>Total (Net)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.data?.map((item) => (
                                    <tr
                                        onClick={() => handleItemSelected(item)}
                                        key={item.id}
                                        className="hover"
                                    >
                                        <td>{item.purchase_order?.po_code}</td>
                                        <td>{item.p_date}</td>
                                        <td>{item.supplier.code}</td>
                                        <td>{item.supplier.name}</td>
                                        <td>{formatIDR(item.amount_cost)}</td>
                                        <td>{item.status}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        <div className="w-full flex justify-center mt-2">
                            <PaginationApi
                                links={data}
                                page={data.current_page}
                                onPageChange={fetch}
                            />
                        </div>
                    </>
                )}
            </Modal>
        </>
    );
}
