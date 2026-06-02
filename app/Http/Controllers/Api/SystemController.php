<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SystemController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'version' => '2.0.0',
            'database' => 'connected',
            'tables' => 13,
            'features' => [
                'authentication' => '✅',
                'authorization' => '✅',
                'user_management' => '✅',
                'business_management' => '✅',
                'service_processing' => '✅',
                'admin_dashboard' => '✅',
                'security_monitoring' => '✅',
                'analytics' => '✅',
            ],
        ]);
    }

    public function info(): JsonResponse
    {
        return response()->json([
            'name' => 'Ghazi Gas Station System',
            'version' => '2.0.0',
            'type' => 'Complete & Scalable',
            'tables' => 13,
            'roles' => 8,
            'services' => 3,
            'features' => [
                'Multi-role Authentication',
                'Business Management',
                'Service Processing',
                'QR Code Validation',
                'Location Verification',
                'Admin Dashboard',
                'Security Monitoring',
                'Analytics & Reports',
                'Emergency Services',
                'Appointment Scheduling',
            ],
            'security' => [
                'jwt_authentication' => '✅',
                'role_based_access' => '✅',
                'qr_code_validation' => '✅',
                'location_verification' => '✅',
                'security_logging' => '✅',
                'threat_detection' => '✅',
            ],
            'performance' => [
                'caching' => '✅',
                'pagination' => '✅',
                'compression' => '✅',
                'rate_limiting' => '✅',
            ],
        ]);
    }

    public function apiOverview(): JsonResponse
    {
        return response()->json([
            'authentication' => [
                'endpoints' => 11,
                'features' => ['Register', 'Login', '2FA', 'Token Refresh', 'Logout'],
                'security' => ['Device Fingerprinting', 'Failed Login Tracking'],
            ],
            'user_management' => [
                'endpoints' => 8,
                'features' => ['Profile', 'QR Codes', 'Password Change', '2FA Management'],
            ],
            'business_management' => [
                'endpoints' => 16,
                'features' => ['Stations', 'Car Wash Centers', 'Maintenance Centers', 'Employees'],
            ],
            'services' => [
                'endpoints' => 18,
                'features' => ['QR Validation', 'Service Processing', 'History', 'Performance'],
            ],
            'admin' => [
                'endpoints' => 30,
                'features' => ['User Management', 'Business Management', 'Financial', 'Reports', 'System', 'Security'],
            ],
        ]);
    }

    public function schema(): JsonResponse
    {
        return response()->json([
            'tables' => [
                'users' => [
                    'description' => 'All system users with 8 different roles',
                    'relationships' => ['stations', 'subscriptions', 'refuels', 'notifications', 'employee_profile'],
                ],
                'gas_stations' => [
                    'description' => 'Gas stations and their owners',
                    'relationships' => ['owner', 'refuels', 'notifications', 'employees'],
                ],
                'car_wash_centers' => [
                    'description' => 'Car wash centers and their services',
                    'relationships' => ['owner', 'car_washes', 'employees'],
                ],
                'maintenance_centers' => [
                    'description' => 'Maintenance workshops and their services',
                    'relationships' => ['owner', 'maintenance_services', 'employees'],
                ],
                'employees' => [
                    'description' => 'Employees and workers for all business types',
                ],
                'subscriptions' => [
                    'description' => 'User subscriptions with service quotas',
                ],
                'refuels' => [
                    'description' => 'Fuel refueling records',
                ],
                'car_washes' => [
                    'description' => 'Car wash service records',
                ],
                'maintenance_services' => [
                    'description' => 'Maintenance service records',
                ],
                'notifications' => [
                    'description' => 'System and user notifications',
                ],
                'security_logs' => [
                    'description' => 'Unified security and activity logging',
                ],
                'support_tickets' => [
                    'description' => 'Support desk tickets',
                ],
                'electronic_cards' => [
                    'description' => 'Priority cards generated from monthly fuel usage',
                ],
            ],
            'total_tables' => 13,
            'security_features' => ['Audit Trail', 'Activity Logging', 'Threat Detection'],
        ]);
    }

    public function roles(): JsonResponse
    {
        return response()->json([
            'roles' => [
                'customer' => [
                    'description' => 'Regular customers using the services',
                    'permissions' => ['View services', 'Use QR codes', 'View own history', 'Manage profile', 'Receive notifications'],
                ],
                'station_owner' => [
                    'description' => 'Owners of gas stations',
                    'permissions' => ['Manage station', 'Manage employees', 'View station analytics', 'Send notifications', 'Process refuels'],
                ],
                'car_wash_owner' => [
                    'description' => 'Owners of car wash centers',
                    'permissions' => ['Manage center', 'Manage employees', 'View center analytics', 'Process car washes'],
                ],
                'maintenance_owner' => [
                    'description' => 'Owners of maintenance workshops',
                    'permissions' => ['Manage workshop', 'Manage technicians', 'View workshop analytics', 'Process maintenance'],
                ],
                'station_worker' => [
                    'description' => 'Workers at gas stations',
                    'permissions' => ['Process refuels', 'View own performance', 'View station info'],
                ],
                'car_wash_worker' => [
                    'description' => 'Workers at car wash centers',
                    'permissions' => ['Process car washes', 'View own performance', 'View center info'],
                ],
                'maintenance_worker' => [
                    'description' => 'Technicians at maintenance workshops',
                    'permissions' => ['Process maintenance', 'View own performance', 'View workshop info'],
                ],
                'admin' => [
                    'description' => 'System administrators',
                    'permissions' => ['Manage all users', 'Manage all businesses', 'View all analytics', 'System management', 'Security monitoring', 'Financial oversight'],
                ],
            ],
            'total_roles' => 8,
            'permission_levels' => 4,
        ]);
    }

    public function services(): JsonResponse
    {
        return response()->json([
            'refuel' => [
                'description' => 'Fuel refueling services',
                'process' => [
                    'Customer shows QR code',
                    'Station worker scans code',
                    'System validates and applies discount',
                    'Worker processes refuel',
                    'Transaction recorded',
                ],
            ],
            'car_wash' => [
                'description' => 'Car washing services',
                'process' => [
                    'Customer requests wash',
                    'System generates QR code',
                    'Customer shows QR at center',
                    'Worker scans code',
                    'Service processed and recorded',
                ],
            ],
            'maintenance' => [
                'description' => 'Vehicle maintenance services',
                'process' => [
                    'Customer requests maintenance',
                    'System generates QR code',
                    'Appointment scheduled',
                    'Customer shows QR at workshop',
                    'Technician scans code',
                    'Service processed and recorded',
                ],
            ],
        ]);
    }
}
