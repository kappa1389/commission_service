<?php

namespace App\Config;

class CommissionFeeConfig
{
    public const DEPOSIT_COMMISSION_FEE = 0.03;
    public const PRIVATE_USER_WITHDRAW_COMMISSION_FEE = 0.3;
    public const BUSINESS_USER_WITHDRAW_COMMISSION_FEE = 0.5;
    public const PRIVATE_USER_FREE_WITHDRAW_TRANSACTIONS_COUNT = 3;
    public const PRIVATE_USER_FREE_WITHDRAW_TRANSACTIONS_AMOUNT_IN_EURO = 1000;

}