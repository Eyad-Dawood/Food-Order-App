<?php 

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BusinessSettings extends Settings
{
    public int $max_addresses_per_user; 

    public static function group(): string
    {
        return 'business';
    }
}