<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case SIMULATED_CARD = 'simulated_card';
}
