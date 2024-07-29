import {
    HiChartPie,
    HiUser,
    HiCog,
    HiGlobeAlt,
    HiInformationCircle,
    HiListBullet,
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
        name: 'Ekspedisi',
        show: true,
        icon: HiListBullet,
        route: route('expeditions.index'),
        active: 'expeditions.index',
        permission: 'view-expedition',
    },
    {
        name: 'Customer',
        show: true,
        icon: HiListBullet,
        route: route('customers.index'),
        active: 'customers.index',
        permission: 'view-customer',
    },
    {
        name: 'Supplier',
        show: true,
        icon: HiListBullet,
        route: route('suppliers.index'),
        active: 'suppliers.index',
        permission: 'view-supplier',
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
