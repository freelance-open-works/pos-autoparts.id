<?php

namespace App\Constants;

class PermissionConstant
{
    const LIST = [
        ['label' => 'Create User', 'name' => 'create-user'],
        ['label' => 'Update User', 'name' => 'update-user'],
        ['label' => 'View User', 'name' => 'view-user'],
        ['label' => 'Delete User', 'name' => 'delete-user'],

        ['label' => 'Create Role', 'name' => 'create-role'],
        ['label' => 'Update Role', 'name' => 'update-role'],
        ['label' => 'View Role', 'name' => 'view-role'],
        ['label' => 'Delete Role', 'name' => 'delete-role'],

        // #Add New Permission Below!
        ['label' => 'Delete Merk', 'name' => 'delete-brand'],
        ['label' => 'Update Merk', 'name' => 'update-brand'],
        ['label' => 'Create Merk', 'name' => 'create-brand'],
        ['label' => 'View Merk', 'name' => 'view-brand'],

        ['label' => 'Delete Claim', 'name' => 'delete-claim'],
        ['label' => 'Update Claim', 'name' => 'update-claim'],
        ['label' => 'Create Claim', 'name' => 'create-claim'],
        ['label' => 'View Claim', 'name' => 'view-claim'],

        ['label' => 'Delete Sale', 'name' => 'delete-sale'],
        ['label' => 'Update Sale', 'name' => 'update-sale'],
        ['label' => 'Create Sale', 'name' => 'create-sale'],
        ['label' => 'View Sale', 'name' => 'view-sale'],
        ['label' => 'Paksa Ubah Sale', 'name' => 'force-update-sale'], //

        ['label' => 'Delete Purchase', 'name' => 'delete-purchase'],
        ['label' => 'Update Purchase', 'name' => 'update-purchase'],
        ['label' => 'Create Purchase', 'name' => 'create-purchase'],
        ['label' => 'View Purchase', 'name' => 'view-purchase'],
        ['label' => 'Paksa Ubah Purchase', 'name' => 'force-update-purchase'], //

        ['label' => 'Delete Purchase Order', 'name' => 'delete-purchase-order'],
        ['label' => 'Update Purchase Order', 'name' => 'update-purchase-order'],
        ['label' => 'Create Purchase Order', 'name' => 'create-purchase-order'],
        ['label' => 'View Purchase Order', 'name' => 'view-purchase-order'],
        ['label' => 'Paksa Ubah Purchase Order', 'name' => 'force-update-purchase-order'], //

        ['label' => 'Delete Order Toko', 'name' => 'delete-store-order'],
        ['label' => 'Update Order Toko', 'name' => 'update-store-order'],
        ['label' => 'Create Order Toko', 'name' => 'create-store-order'],
        ['label' => 'View Order Toko', 'name' => 'view-store-order'],
        ['label' => 'Paksa Ubah Order Toko', 'name' => 'force-update-store-order'], //

        ['label' => 'Delete Product', 'name' => 'delete-product'],
        ['label' => 'Update Product', 'name' => 'update-product'],
        ['label' => 'Create Product', 'name' => 'create-product'],
        ['label' => 'View Product', 'name' => 'view-product'],

        ['label' => 'Delete Supplier', 'name' => 'delete-supplier'],
        ['label' => 'Update Supplier', 'name' => 'update-supplier'],
        ['label' => 'Create Supplier', 'name' => 'create-supplier'],
        ['label' => 'View Supplier', 'name' => 'view-supplier'],

        ['label' => 'Delete Customer', 'name' => 'delete-customer'],
        ['label' => 'Update Customer', 'name' => 'update-customer'],
        ['label' => 'Create Customer', 'name' => 'create-customer'],
        ['label' => 'View Customer', 'name' => 'view-customer'],

        ['label' => 'Delete Expedition', 'name' => 'delete-expedition'],
        ['label' => 'Update Expedition', 'name' => 'update-expedition'],
        ['label' => 'Create Expedition', 'name' => 'create-expedition'],
        ['label' => 'View Expedition', 'name' => 'view-expedition'],

        // Lonely permission
        ['label' => 'View Dashboard', 'name' => 'view-dashboard'],
        ['label' => 'View Setting', 'name' => 'view-setting'],
        ['label' => 'View Report', 'name' => 'view-report'],
        ['label' => 'View Product Stock', 'name' => 'view-product-stock'],
    ];
}
