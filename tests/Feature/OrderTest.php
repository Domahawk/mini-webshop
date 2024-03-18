<?php

namespace Tests\Feature;

use App\Enums\PriceModifier;
use App\Models\Address;
use App\Models\ContractList;
use App\Models\Order;
use App\Models\PriceListProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Address::factory()->create([
            'user_id' => $this->user
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->user);
    }

    /**
     * @dataProvider createOrderData
     */
    public function test_create_order(array $data, array $expected): void
    {
        $country = $this->user->address->country;
        $country->vat = $data['priceModifiers']['vat'];
        $country->save();

        $requestBody = [];
        $priceListProducts = [];

        foreach ($data['products'] as $product) {
            $newProduct = Product::factory()->create();
            $priceListProducts[$newProduct->id] = PriceListProduct::factory()->create([
                'price_list_id' => $this->user->price_list_id,
                'sku' => $newProduct->sku,
                'price' => $product['price'],
            ]);
            $requestBody['products'][] = [
                'productId' => $newProduct->id,
                'amount' => $product['amount'],
            ];
        }

        foreach ($data['priceModifiers'] as $name => $amount) {
            $modifier = PriceModifier::create($name);

            if ($modifier->isVat() || $modifier->isLargeOrderDiscount()) {
                continue;
            }

            $requestBody['modifiers'][] = $name;
        }

        $this->actingAs($this->user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/orders', $requestBody);

        $response->assertStatus(201);

        $orderId = json_decode($response->getContent(), true)['id'];
        $order = Order::find($orderId);

        $this->assertEquals(
            $expected['orderTotal'],
            $order->total,
            'Expected order total: ' . $expected['orderTotal'] . ' got: ' . $order->total
        );
        $this->assertEquals(
            $expected['vatAmount'],
            $order->vat,
            'Expected vat : ' . $expected['vatAmount'] . ' got: ' . $order->vat
        );
        $this->assertEquals(
            $expected['orderTotalVat'],
            $order->total_vat,
            'Expected order vat and total: ' . $expected['orderTotalVat'] . ' got: ' . $order->total_vat
        );
        $this->assertCount(count($data['products']), $order->orderProducts, 'Different amount of products on order');

        foreach ($order->orderProducts as $orderProduct) {
            $this->assertEquals(
                $priceListProducts[$orderProduct->product_id]->price,
                $orderProduct->price,
                'Price is different from price list price'
            );
        }

        $priceModifiers = $order->priceModifiers;
        $this->assertCount(
            count($expected['priceModifiers']),
            $priceModifiers,
            'Expected 1 price modifier, got: ' . $priceModifiers->count()
        );

        /** @var PriceModifier $priceModifier */
        foreach ($priceModifiers as $priceModifier) {
            $this->assertArrayHasKey(
                $priceModifier->name,
                $expected['priceModifiers'],
                "Price modifier $priceModifier->name was not expected"
            );
        }
    }

    /**
     * @dataProvider dataValidationData
     */
    public function test_create_order_data_validation(bool $badPriceList, array $requestBody, array $expected): void
    {
        $this->actingAs($this->user);
        $expectedMessage = $expected['message'];

        if ($requestBody['products'][0]['productId']) {
            $newProduct = Product::factory()->create();
            $priceListProductData = ['sku' => $newProduct->sku];
            $requestBody['products'][0]['productId'] = $newProduct->id;
            $priceListProductData['price_list_id'] = $this->user->price_list_id;

            if ($badPriceList) {
                unset($priceListProductData['price_list_id']);
                $expectedMessage .= $newProduct->id;
            }


            PriceListProduct::factory()->create($priceListProductData);
        }

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/orders', $requestBody);

        $response->assertStatus($expected['code']);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseBody);
        $this->assertArrayHasKey('errors', $responseBody);
        $this->assertEquals($expectedMessage, $responseBody['message'], 'Wrong message in body');

        $this->assertEquals($expected['errors'], $responseBody['errors'], 'Wrong error');
    }

    public function test_create_order_with_contract_list_price(): void
    {
        $requestBody = [];

        $newProduct = Product::factory()->create();
        PriceListProduct::factory()->create([
            'price_list_id' => $this->user->price_list_id,
            'sku' => $newProduct->sku,
            'price' => 40.00,
        ]);

        $contractList = ContractList::factory()->create([
            'user_id' => $this->user->id,
            'sku' => $newProduct->sku,
        ]);

        $requestBody['products'][] = [
            'productId' => $newProduct->id,
            'amount' => 2,
        ];

        $this->actingAs($this->user);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/orders', $requestBody);

        $response->assertStatus(201);

        $orderId = json_decode($response->getContent(), true)['id'];
        $order = Order::find($orderId);
        $orderProducts = $order->orderProducts;

        $this->assertCount(1, $orderProducts, 'Wrong product count');
        $this->assertEquals($orderProducts->first()->price, $contractList->price, 'Incorrect product price');
    }

    public static function createOrderData(): array
    {
        $vat = PriceModifier::VAT;
        $largeOrderDiscount = PriceModifier::LARGE_ORDER_DISCOUNT;
        $seasonalDiscount = PriceModifier::SEASONAL_DISCOUNT;
        $testCases = [
            [
                [
                    'priceModifiers' => [
                        $vat->value => $vat->getModifierAmount(),
                    ],
                    'products' => [
                        [
                            'price' => 20.00,
                            'amount' => 1
                        ],
                        [
                            'price' => 35.00,
                            'amount' => 2
                        ],
                    ]
                ],
                'expected' => []
            ],
            [
                [
                    'priceModifiers' => [
                        $vat->value => $vat->getModifierAmount(),
                        $largeOrderDiscount->value => $largeOrderDiscount->getModifierAmount(),
                    ],
                    'products' => [
                        [
                            'price' => 20.00,
                            'amount' => 1
                        ],
                        [
                            'price' => 45.00,
                            'amount' => 2
                        ],
                    ]
                ],
                'expected' => []
            ],
            [
                [
                    'priceModifiers' => [
                        $vat->value => $vat->getModifierAmount(),
                        $seasonalDiscount->value => $seasonalDiscount->getModifierAmount(),
                    ],
                    'products' => [
                        [
                            'price' => 20.00,
                            'amount' => 1
                        ],
                        [
                            'price' => 35.00,
                            'amount' => 2
                        ],
                    ]
                ],
                'expected' => []
            ],
            [
                [
                    'priceModifiers' => [
                        $vat->value => $vat->getModifierAmount(),
                        $largeOrderDiscount->value => $largeOrderDiscount->getModifierAmount(),
                        $seasonalDiscount->value => $seasonalDiscount->getModifierAmount(),
                    ],
                    'products' => [
                        [
                            'price' => 20.00,
                            'amount' => 1
                        ],
                        [
                            'price' => 45.00,
                            'amount' => 2
                        ],
                    ]
                ],
                'expected' => []
            ],
        ];

        foreach ($testCases as $index => $testCase) {
            $orderTotal = 0;

            foreach ($testCase[0]['products'] as $product) {
                $orderTotal += $product['price'] * $product['amount'];
            }

            $priceModifiers = [];
            $discount = 0;

            foreach ($testCase[0]['priceModifiers'] as $key => $amount) {
                $priceModifier = PriceModifier::create($key);

                if ($priceModifier->isVat() || $priceModifier->isLargeOrderDiscount()) {
                    continue;
                }

                $priceModifiers[$priceModifier->value] = $amount;

                if ($priceModifier->getModifierType()->isPercent()) {
                    $priceModifiers[$priceModifier->value] = self::calculatePercent($amount, $orderTotal);
                }

                $discount += $priceModifiers[$priceModifier->value];
            }

            $orderTotal = $orderTotal - $discount;

            if (isset($testCase[0]['priceModifiers'][$largeOrderDiscount->value])) {
                $amount = $testCase[0]['priceModifiers'][$largeOrderDiscount->value];
                $priceModifiers[$largeOrderDiscount->value] = self::calculatePercent($amount, $orderTotal);

                $orderTotal -= $priceModifiers[$largeOrderDiscount->value];
            }

            $priceModifiers[$vat->value] = self::calculatePercent($testCase[0]['priceModifiers'][$vat->value], $orderTotal);

            $testCases[$index]['expected'] = [
                'orderTotal' => $orderTotal,
                'vatAmount' => $priceModifiers[$vat->value],
                'orderTotalVat' => $orderTotal + $priceModifiers[$vat->value],
                'priceModifiers' => $priceModifiers,
            ];
        }

        return $testCases;
    }

    public static function dataValidationData(): array
    {
        return [
            // unsupported_price_modifier
            [
                'badPriceList' => false,
                'requestBody' => [
                    'products' => [
                        [
                            'productId' => true,
                            'amount' => 20,
                        ],
                    ],
                    'modifiers' => [
                        'unsupportedModifier',
                    ],
                ],
                'expected' => [
                    'message' => 'Fail to create order',
                    'errors' => [
                        'Price modifier unsupportedModifier is not supported'
                    ],
                    'code' => 400,
                ]
            ],
            // invalid productId passed (not uuid)
            [
                'badPriceList' => false,
                'requestBody' => [
                    'products' => [
                        [
                            'productId' => false,
                            'amount' => 20,
                        ],
                    ],
                ],
                'expected' => [
                    'message' => 'The products.0.productId field must be a valid UUID.',
                    'errors' => [
                        "products.0.productId" => [
                            "The products.0.productId field must be a valid UUID."
                        ]
                    ],
                    'code' => 422,
                ]
            ],
            // invalid amount
            [
                'badPriceList' => false,
                'requestBody' => [
                    'products' => [
                        [
                            'productId' => true,
                            'amount' => 'wrong amount',
                        ],
                    ],
                ],
                'expected' => [
                    'message' => 'The products.0.amount field must be an integer.',
                    'errors' => [
                        "products.0.amount" => [
                            "The products.0.amount field must be an integer."
                        ]
                    ],
                    'code' => 422,
                ]
            ],
            // user does not have product in his price list
            [
                'badPriceList' => true,
                'requestBody' => [
                    'products' => [
                        [
                            'productId' => true,
                            'amount' => 20,
                        ],
                    ],
                ],
                'expected' => [
                    'message' => 'Cannot place an order for products: ',
                    'errors' => [],
                    'code' => 400,
                ]
            ],
        ];
    }

    private static function calculatePercent(int $value, float $orderTotal): float
    {
        return round(
            $orderTotal * ($value / 100),
            2,
            PHP_ROUND_HALF_DOWN
        );
    }
}
