<?php

namespace App\Enum;

enum PaymentMethod: string
{
    case Card = 'card';
    case Cash = 'cash';
    case Cheque = 'cheque';
    case Transfer = 'transfer';
    case OnlineLink = 'online_link';
}
