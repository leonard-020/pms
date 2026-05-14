<?php

return [
    // Super Admin — full system control (technical)
    'super_admin' => [
        'users.*',
        'users.activate',
        'users.deactivate',
        'roles.*',
        'permissions.*',
        'system.settings',
        'audit_logs.view',
        'backup.manage',
    ],

    // System Auditor — read-only monitoring
    'system_auditor' => [
        'audit_logs.view',
        'reports.view',
        'finance.view',
        'members.view',
    ],

    // Parish Priest — leadership & approvals
    'parish_priest' => [
        'members.*',
        'sacraments.*',
        'reports.*',
        'finance.view',
        'finance.approve',
        'users.deactivate',
    ],

    // Parish Secretary — administration
    'parish_secretary' => [
        'members.create',
        'members.update',
        'members.view',
        'sacraments.create',
        'sacraments.update',
        'events.*',
    ],

    // Finance Officer — money management
    'finance_officer' => [
        'finance.create',
        'finance.update',
        'finance.view',
        'reports.finance',
    ],

    // Ministry Leader — groups & attendance
    'ministry_leader' => [
        'groups.*',
        'attendance.*',
        'members.view',
        'events.view',
    ],

    // Parish Member — self-service
    'parish_member' => [
        'profile.view',
        'profile.update',
        'events.view',
        'requests.create',
    ],
];