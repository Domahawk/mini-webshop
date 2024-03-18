<?php

namespace Tests\Feature;

use App\Enums\Product\Filter;
use App\Enums\Product\Sort;
use App\Models\Category;
use App\Models\ContractList;
use App\Models\PriceListProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->user);
    }

    /**
     * @dataProvider listAllProductData
     */
    public function test_list_all_products(int $userProductNumber, int|null $perPage): void
    {
        $query = '';
        $products = Product::factory()->count($userProductNumber)->create();
        $priceListProducts = [];

        foreach ($products as $product) {
            $priceListProducts[$product->sku] = PriceListProduct::factory()->create([
                'sku' => $product->sku,
                'price_list_id' => $this->user->price_list_id
            ]);
        }

        Product::factory()->count(10)->create();

        if (!empty($perPage)) {
            $query = '?perPage=' . $perPage;
        }

        $this->actingAs($this->user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get('/api/products' . $query);

        $response->assertStatus(200);
        $data = $response['data'];

        foreach ($data as $product) {
            $this->assertArrayHasKey($product['sku'], $priceListProducts, 'Product sku not in users price list');
            /** @var PriceListProduct $expectedProduct */
            $expectedProduct = $priceListProducts[$product['sku']];
            $this->assertEquals($product['price'], $expectedProduct->price, 'Product has wrong price');
        }

        $this->assertEquals($userProductNumber, $response['total'], "Expected $userProductNumber products in response");
    }

    public function test_show_product_and_user_sees_contract_list_price(): void
    {
        $product = Product::factory()->create();
        PriceListProduct::factory()->create([
            'sku' => $product->sku,
            'price_list_id' => $this->user->price_list_id,
            'price' => 100.00
        ]);
        $contractListProduct = ContractList::factory()->create([
            'sku' => $product->sku,
            'user_id' => $this->user->id,
            'price' => 50.00
        ]);

        $this->actingAs($this->user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get('/api/products/' . $product->id);

        $response->assertStatus(200);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(
            $product->sku,
            $data['sku'], 'Product skus does not match');
        $this->assertEquals(
            $contractListProduct->price,
            $data['price'],
            'Product price does not match price on contract list'
        );
    }

    /**
     * @dataProvider filterProductsData
     */
    public function test_filter_products(
        Filter $filter,
        string|float $value,
        float $price,
        string $name,
        string $category
    ): void
    {
        $product = Product::factory()->create([
            'name' => $name,
        ]);
        $newCategory = Category::factory()->create([
            'name' => $category,
        ]);
        $product->categories()->attach($newCategory->id);
        $priceListProduct = PriceListProduct::factory()->create([
            'sku' => $product->sku,
            'price_list_id' => $this->user->price_list_id,
            'price' => $price
        ]);

        $userProducts = Product::factory(3)->create();

        foreach ($userProducts as $userProduct) {
            PriceListProduct::factory()->create([
                'sku' => $userProduct->sku,
                'price_list_id' => $this->user->price_list_id,
                'price' => $price * rand(2, 4)
            ]);
        }

        Product::factory(3)->create();

        $this->actingAs($this->user);
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get("/api/products/filter?filter[$filter->value]=$value");

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true);
        $this->assertCount(1, $data);
        $this->assertEquals($priceListProduct->price, $data[0]['price'], 'Price is not correct');
        $this->assertEquals($product->sku, $data[0]['sku'], 'Price is not correct');
        $this->assertEquals($product->name, $data[0]['name'], 'Price is not correct');
    }

    public static function listAllProductData(): array
    {
        return [
            // user can see 0 products
            [
                'userProductNumber' => 0,
                'perPage' => null
            ],
            // user can see 3 products
            [
                'userProductNumber' => 5,
                'perPage' => null
            ],
        ];
    }

    public static function filterProductsData(): array
    {
        return [
            [
                'filter' => Filter::NAME,
                'value' => 'Test product name filter',
                'price' => 20.00,
                'name' => 'Test product name filter',
                'category' => 'Test product name filter',
            ],
            [
                'filter' => Filter::PRICE,
                'value' => 20.00,
                'price' => 20.00,
                'name' => 'Test product price filter',
                'category' => 'Test product price filter',
            ],
            [
                'filter' => Filter::CATEGORY,
                'value' => 'Test Category filter',
                'price' => 20.00,
                'name' => 'Test product 1',
                'category' => 'Test Category filter',
            ]
        ];
    }

    public static function sortProductsProvider(): array
    {
        return [
            [
                'sort' => Sort::NAME,
                'value' => 'asc',
                'name' => 'Test',
                'price' => 20.00,
            ],
            [
                'sort' => Sort::PRICE,
                'value' => 'asc',
                'name' => 'Test',
                'price' => 20.00,
            ],
            [
                'sort' => Sort::NAME,
                'value' => 'desc',
                'name' => 'Test',
                'price' => 20.00,
            ],
            [
                'sort' => Sort::PRICE,
                'value' => 'desc',
                'name' => 'Test',
                'price' => 20.00,
            ]
        ];
    }
}
