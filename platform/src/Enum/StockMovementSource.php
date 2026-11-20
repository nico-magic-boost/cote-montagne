<?php

namespace App\Enum;

enum StockMovementSource: string
{
    case Manual = 'manual';
    case Skilou = 'skilou';
    case Website = 'website';
    case Quote = 'quote';
}
