import {
    HiChartPie,
    HiUser,
    HiCog,
    HiGlobeAlt,
    HiInformationCircle,
    HiListBullet,
    HiShoppingBag,
    HiOutlineArchiveBox,
} from 'react-icons/hi2'

export default [
    {
        name: 'Dashboard',
        show: true,
        icon: HiChartPie,
        route: route('dashboard'),
        active: 'dashboard',
        permission: 'view-dashboard',
    },
    {
        name: 'Pemesanan (PO)',
        show: true,
        icon: HiOutlineArchiveBox,
        route: route('purchase-orders.index'),
        active: 'purchase-orders.*',
        permission: 'view-purchase-order',
    },
    {
        name: 'Data',
        show: true,
        icon: HiListBullet,
        items: [
            {
                name: 'Ekspedisi',
                show: true,
                route: route('expeditions.index'),
                active: 'expeditions.index',
                permission: 'view-expedition',
            },
            {
                name: 'Customer',
                show: true,
                route: route('customers.index'),
                active: 'customers.index',
                permission: 'view-customer',
            },
            {
                name: 'Supplier',
                show: true,
                route: route('suppliers.index'),
                active: 'suppliers.index',
                permission: 'view-supplier',
            },
            {
                name: 'Barang',
                show: true,
                route: route('products.index'),
                active: 'products.index',
                permission: 'view-product',
            },
        ],
    },

    {
        name: 'Stok',
        show: true,
        icon: HiListBullet,
        route: route('product-stocks.index'),
        active: 'product-stocks.index',
        permission: 'view-product-stock',
    },
    {
        name: 'User',
        show: true,
        icon: HiUser,
        items: [
            {
                name: 'Roles',
                show: true,
                route: route('roles.index'),
                active: 'roles.*',
                permission: 'view-role',
            },
            {
                name: 'Users',
                show: true,
                route: route('user.index'),
                active: 'user.index',
                permission: 'view-user',
            },
        ],
    },
    {
        name: 'Setting',
        show: true,
        icon: HiCog,
        route: route('setting.index'),
        active: 'setting.index',
        permission: 'view-setting',
    },
]
