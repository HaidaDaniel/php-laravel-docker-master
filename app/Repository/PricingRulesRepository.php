<?php

namespace App\Repository;

use Carbon\Carbon;

/**
 * Handles retrieval of pricing rules from the 'pricing_rules' table.
 */
class PricingRulesRepository
{
    /**
     * Returns all pricing rules from the database, regardless of active status or dates.
     *
     * @param \PDO $db
     * @return array<string,array<string,mixed>>
     */
    public function getAllRules(\PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM pricing_rules");
        $results = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = [
                'product_code' => $row['product_code'],
                'rule_name'    => $row['rule_name'],
                'rule_derails' => $row['rule_derails'],
                'active'       => (bool)$row['active'],
                'start_date'   => $row['start_date'],
                'end_date'     => $row['end_date'],
                'days'         => $row['days'],
            ];
        }

        return $results;
    }

    /**
     * Returns only rules that are active in the database and also pass date/day checks.
     *
     * @param \PDO $db
     * @return array<string,array<string,mixed>>
     */
    public function getActiveRules(\PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM pricing_rules WHERE active = TRUE");
        $results = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!$this->isRuleActive($row)) {
                continue;
            }

            $results[$row['product_code']] = [
                'rule_name'    => $row['rule_name'],
                'rule_details' => json_decode($row['rule_details'], true) ?? []
            ];
        }

        return $results;
    }

    /**
     * Returns only rules that are active and valid for the given product codes.
     *
     * @param \PDO $db
     * @param string[] $codes
     * @return array<string,array<string,mixed>>
     */
    public function getActiveRulesByProductCodes(\PDO $db, array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $placeholders = rtrim(str_repeat('?,', count($codes)), ',');
        $sql = "SELECT * FROM pricing_rules 
                WHERE product_code IN ($placeholders)
                  AND active = TRUE";

        $stmt = $db->prepare($sql);
        $stmt->execute($codes);

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!$this->isRuleActive($row)) {
                continue;
            }

            $results[$row['product_code']] = [
                'rule_name'    => $row['rule_name'],
            ];
        }

        return $results;
    }

    /**
     * Determines if a rule is currently valid based on date range and allowed days.
     *
     * @param array<string,mixed> $row
     * @return bool
     */
    protected  function isRuleActive(array $row): bool
    {
        $currentDate = Carbon::now();
        $currentDay  = $currentDate->format('l');

        if (isset($row['start_date']) && !empty($row['start_date'])) {
            if ($currentDate < new \DateTime($row['start_date'])) {
                return false;
            }
        }

        if (isset($row['end_date']) && !empty($row['end_date'])) {
            if ($currentDate > new \DateTime($row['end_date'])) {
                return false;
            }
        }

        if (!empty($row['days'])) {
            $days = json_decode($row['days'], true);
            if (is_array($days) && !in_array($currentDay, $days)) {
                return false;
            }
        }

        return true;
    }
}
