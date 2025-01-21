<?php

namespace App\Enums;

/**
 * An Enum representing different types of pricing rules.
 */
enum PricingRuleType: string
{
    case BUY_ONE_GET_ONE = 'buy_one_get_one';
    case BULK_DISCOUNT   = 'bulk_discount';


    /**
     * Applies the logic for the given pricing rule type.
     *
     * @param int $quantity The quantity of items.
     * @param float $price The base price of a single item.
     * @param array $details Additional details needed for the rule.
     * @return float The calculated total price after applying the rule.
     */
    public function apply(int $quantity, float $price, array $details): float
    {
        return match ($this) {
            self::BUY_ONE_GET_ONE =>
                $price * (intdiv($quantity, 2) + $quantity % 2),

            self::BULK_DISCOUNT =>
                ($quantity >= ($details['min_quantity'] ?? 3))
                    ? ($details['discount_price'] * $quantity)
                    : ($price * $quantity),
        };
    }
}
