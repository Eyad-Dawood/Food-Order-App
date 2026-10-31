<?php

namespace App\Enums;

enum CouponType: string
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';
}
