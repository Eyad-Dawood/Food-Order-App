<?php

namespace App\Traits\Normalizations;
trait NormalizesPhoneNumber
{
    protected function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '01')) {
                $phone = '+2' . $phone;
            } 
            elseif (str_starts_with($phone, '201')) {
                $phone = '+' . $phone;
            }

        return $phone;
    }
}