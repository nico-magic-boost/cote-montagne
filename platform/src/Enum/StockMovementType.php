<?php

namespace App\Enum;

enum StockMovementType: string
{
    case SupplierReceipt = 'supplier_receipt';
    case StoreSale = 'store_sale';
    case OnlineSale = 'online_sale';
    case QuoteDelivery = 'quote_delivery';
    case CustomerReturn = 'customer_return';
    case TransferOut = 'transfer_out';
    case TransferIn = 'transfer_in';
    case InventoryAdjustment = 'inventory_adjustment';
}
