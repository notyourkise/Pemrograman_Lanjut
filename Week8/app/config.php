<?php

/**
 * Week 8: Application Configuration
 * Database connection settings and constants
 */

return [
    // Database configuration
    'database' => [
        'host' => 'localhost',
        'dbname' => 'hospital',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],

    // Application settings
    'app' => [
        'name' => 'Hospital Management System',
        'version' => '8.0',
        'timezone' => 'Asia/Jakarta',
        'session_lifetime' => 7200, // 2 hours in seconds
        'base_url' => '/MATERI-ASDOS/Week8/public',
    ],

    // Security settings
    'security' => [
        'session_name' => 'HOSPITAL_SESSION',
        'password_min_length' => 8,
        'login_max_attempts' => 5,
        'lockout_time' => 900, // 15 minutes in seconds
    ],

    // Pagination
    'pagination' => [
        'per_page' => 10,
        'max_per_page' => 100,
    ],

    // Roles and permissions
    'roles' => [
        'admin' => [
            'label' => 'Administrator',
            'permissions' => ['*'] // All permissions
        ],
        'doctor' => [
            'label' => 'Doctor',
            'permissions' => [
                'appointments.view',
                'appointments.update',
                'patients.view',
                'patients.create',
                'patients.update'
            ]
        ],
        'receptionist' => [
            'label' => 'Receptionist',
            'permissions' => [
                'appointments.view',
                'appointments.create',
                'appointments.update',
                'patients.view',
                'patients.create',
                'patients.update'
            ]
        ]
    ]
];
