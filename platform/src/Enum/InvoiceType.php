<?php

namespace App\Enum;

enum InvoiceType: string
{
    case Full = 'full';
    case Deposit = 'deposit';
    case Balance = 'balance';
    case CreditNote = 'credit_note';
}
