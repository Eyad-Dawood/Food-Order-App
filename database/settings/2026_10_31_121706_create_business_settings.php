<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('business.max_addresses_per_user', 10); 
        $this->migrator->add('business.max_restaurants_per_user', 5); 
    }
};
