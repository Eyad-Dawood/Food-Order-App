<?php 

namespace App\Enums\Auth;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case Restaurant_Manager = 'restaurant_manager';
}