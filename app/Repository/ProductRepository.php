<?php

namespace App\Repository;

class ProductRepository
{
    /**
     * Retrieves all products from the 'products' table.
     *
     * @param \PDO $db
     * @return array<string,array<string,mixed>>
     */
    public function getAllProducts(\PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM products");
        $products = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $code = $row['code'];
            $products[$code] = [
                'code'  => $code,
                'name'  => $row['name'],
                'price' => (float)$row['price'],
            ];
        }

        return $products;
    }

    /**
     * Retrieves only the products matching specific codes.
     *
     * @param \PDO $db
     * @param string[] $codes
     * @return array<string,array<string,mixed>>
     */
    public function getProductsByCodes(\PDO $db, array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $placeholders = rtrim(str_repeat('?,', count($codes)), ',');
        $sql = "SELECT * FROM products WHERE code IN ($placeholders)";
        $stmt = $db->prepare($sql);
        $stmt->execute($codes);

        $products = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $code = $row['code'];
            $products[$code] = [
                'code'  => $code,
                'name'  => $row['name'],
                'price' => (float)$row['price'],
            ];
        }

        return $products;
    }
}
