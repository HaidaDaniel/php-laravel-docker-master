<?php

namespace App\Services;

use App\Enums\PricingRuleType;

/**
 * Manages the shopping cart and applies pricing rules using Enum.
 */
class Checkout
{
    /**
     * @var array<string,array<string,mixed>>
     */
    private array $products = [];

    /**
     * @var array<string,array<string,mixed>>
     */
    private array $rules = [];

    /**
     * @var string[]
     */
    private array $cart = [];

    /**
     * Initializes the Checkout with an optional set of rules.
     *
     * @param array<string,array<string,mixed>> $pricingRules
     */
    public function __construct(array $pricingRules = [])
    {
        $this->rules = $pricingRules;
    }

    /**
     * Adds a product to the cart by its code.
     *
     * @param string $productCode
     * @throws \Exception
     */
    public function scan(string $productCode): void
    {
        if (!isset($this->products[$productCode])) {
            throw new \Exception("Product not found: $productCode");
        }

        $this->cart[] = $productCode;
    }

    /**
     * Calculates the total cost of items in the cart.
     *
     * @return float
     */
    public function total(): float
    {
        $total = 0.0;
        $counts = array_count_values($this->cart);

        foreach ($counts as $productCode => $quantity) {
            $price = $this->products[$productCode]['price'];
            $rule  = $this->rules[$productCode] ?? null;

            if ($rule) {
                $total += $this->applyRule($rule, $quantity, $price);
            } else {
                $total += $quantity * $price;
            }
        }

        return round($total, 2);
    }

    /**
     * Applies a pricing rule to a set of items.
     *
     * @param array<string,mixed> $rule
     * @param int $quantity
     * @param float $price
     * @throws \Exception
     * @return float
     */
    private function applyRule(array $rule, int $quantity, float $price): float
    {
        $ruleName    = $rule['rule_name'];
        $ruleDetails = $rule['rule_details'] ?? [];

        try {
            $ruleType = PricingRuleType::from($ruleName);
        } catch (\ValueError) {
            throw new \Exception("Unknown rule: $ruleName");
        }

        // Delegate logic to the Enum's apply() method
        return $ruleType->apply($quantity, $price, $ruleDetails);
    }

    /**
     * Sets the available products data from an external source.
     *
     * @param array<string,array<string,mixed>> $products
     */
    public function loadProducts(array $products): void
    {
        $this->products = $products;
    }

    /**
     * Sets the current active rules from an external source.
     *
     * @param array<string,array<string,mixed>> $rules
     */
    public function loadRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * Empties the cart.
     */
    public function clearCart(): void
    {
        $this->cart = [];
    }

    /**
     * Returns the contents of the cart.
     *
     * @return string[]
     */
    public function getCart(): array
    {
        return $this->cart;
    }
}
