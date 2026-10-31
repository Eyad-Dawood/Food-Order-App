<?php


namespace App\Traits\Normalizations;
trait NormalizesTime
{
    protected function normalizeTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        $time = preg_replace('/[^0-9:]/', '', $time);

        if (preg_match('/^\d:\d{2}$/', $time)) {
            $time = '0' . $time;
        }

        return $time;
    }
}