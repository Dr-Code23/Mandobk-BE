<?php

return [
    'table_name' => 'roles',
    'signup_roles' => ['doctor', 'company', 'pharmacy', 'storehouse'],
    'all_roles' => [
        'ceo',
        'data_entry',
        'monitor_and_evaluation',
        'order_management',
        'human_resource',
        'markting',
        'doctor',
        'company',
        'pharmacy',
        'pharmacy_sub_user',
        'storehouse',
        'visitor',
    ],
    'monitor_roles' => [
        'data_entry',
        'monitor_and_evaluation',
        'order_management',
        'human_resource',
        'markting',
    ],
    'human_resources_roles' => [
        'data_entry',
        'monitor_and_evaluation',
        'order_management',
        'human_resource',
        'markting',
    ],
    'patch_number_roles' => [
        'ceo', 'data_entry', 'company', 'storehouse', 'pharmacy', 'pharmacy_sub_user',
    ],
    'admin_product_role' => [
        'ceo', 'data_entry',
    ],
    'role_patch_number_symbol' => [
        'ceo' => 'ad',
        'data_entry' => 'ad',
        'company' => 'M',
        'storehouse' => 'MK',
        'pharmacy' => 'IN',
        'pharmacy_sub_user' => 'IN',
    ],
    'rolesHasSubUsers' => ['pharmacy']
];
