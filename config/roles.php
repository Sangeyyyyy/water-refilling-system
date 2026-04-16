<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Roles
    |--------------------------------------------------------------------------
    |
    | Define the role groups used throughout the system for permissions
    | and dashboard access.
    |
    */

    'admin_roles' => ['admin', 'director', 'manager', 'staff'],
    
    'management_roles' => ['admin', 'director', 'manager'],
    
    'inventory_managers' => ['admin', 'superadmin', 'manager', 'director', 'unit_admin'],
    
    'inventory_editors' => ['admin', 'superadmin', 'manager', 'director'],
    
    'inventory_admins' => ['admin', 'superadmin'],
];
