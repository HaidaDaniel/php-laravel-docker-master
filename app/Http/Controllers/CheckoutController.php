<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Checkout;
use App\Repository\ProductRepository;
use App\Repository\PricingRulesRepository;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Supermarket Checkout API",
 *     description="API for calculating checkout totals with discounts",
 * )
 */
class CheckoutController extends Controller
{
    /**
     * @OA\Get(
     *     path="/checkout",
     *     summary="Get checkout total",
     *     description="Calculates the total price of scanned products after applying active pricing rules.",
     *     @OA\Parameter(
     *         name="products",
     *         in="query",
     *         required=true,
     *         description="Comma-separated product codes (e.g., FR1,SR1,FR1,CF1)",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product_codes", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="total", type="number", format="float"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *         ),
     *     )
     * )
     */
    public function checkoutCart(Request $request)
    {
        $productCodesParam = $request->query('products');

        if (!$productCodesParam) {
            return response()->json([
                'error' => 'No product codes provided. Use ?products=FR1,SR1,...'
            ], 400);
        }

        $productCodes = array_map('trim', explode(',', $productCodesParam));

        if (empty($productCodes)) {
            return response()->json([
                'error' => 'No valid product codes found.'
            ], 400);
        }

        $pdo = new \PDO(
            sprintf(
                'pgsql:host=%s;dbname=%s;port=%s',
                config('database.connections.pgsql.host'),
                config('database.connections.pgsql.database'),
                config('database.connections.pgsql.port')
            ),
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.password')
        );
        
        $productRepo = new ProductRepository();
        $rulesRepo = new PricingRulesRepository();

        $allProducts = $productRepo->getAllProducts($pdo);
        $activeRules = $rulesRepo->getActiveRules($pdo);

        $checkout = new Checkout($activeRules);
        $checkout->loadProducts($allProducts);

        foreach ($productCodes as $code) {
            try {
                $checkout->scan($code);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }

        $total = $checkout->total();

        return response()->json([
            'product_codes' => $productCodes,
            'total' => $total,
        ]);
    }
}
