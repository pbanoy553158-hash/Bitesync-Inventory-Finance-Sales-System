<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MultiItemStockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_out_records_multiple_items_in_one_transaction(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItems = $this->createInventoryItems([10, 6]);

        $response = $this->actingAs($user)->post(
            route('inventory.stock-out.store'),
            [
                'items' => [
                    [
                        'inventory_item_id' => $inventoryItems[0]->id,
                        'quantity' => 2,
                    ],
                    [
                        'inventory_item_id' => $inventoryItems[1]->id,
                        'quantity' => 1.5,
                    ],
                ],
                'reason_category' => 'used',
                'reason' => 'Used to prepare customer orders.',
            ]
        );

        $stockOut = StockOut::with('items')->firstOrFail();

        $response->assertRedirect(route('inventory.stock', [
            'inventoryItem' => $inventoryItems[0],
            'operation' => 'stock-out',
        ]));
        $this->assertCount(2, $stockOut->items);
        $this->assertSame('used', $stockOut->reason_category);
        $this->assertSame('8.00', $inventoryItems[0]->fresh()->quantity);
        $this->assertSame('4.50', $inventoryItems[1]->fresh()->quantity);
        $this->assertDatabaseCount('stock_movements', 2);
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => StockOut::class,
            'reference_id' => $stockOut->id,
        ]);
    }

    public function test_stock_out_rolls_back_all_items_if_one_quantity_is_invalid(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItems = $this->createInventoryItems([10, 1]);

        $response = $this->actingAs($user)->post(
            route('inventory.stock-out.store'),
            [
                'items' => [
                    [
                        'inventory_item_id' => $inventoryItems[0]->id,
                        'quantity' => 2,
                    ],
                    [
                        'inventory_item_id' => $inventoryItems[1]->id,
                        'quantity' => 2,
                    ],
                ],
                'reason_category' => 'damaged',
                'reason' => 'Packaging was damaged during storage.',
            ]
        );

        $response->assertSessionHasErrors('items.1.quantity');
        $this->assertSame('10.00', $inventoryItems[0]->fresh()->quantity);
        $this->assertSame('1.00', $inventoryItems[1]->fresh()->quantity);
        $this->assertDatabaseCount('stock_outs', 0);
        $this->assertDatabaseCount('stock_out_items', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_stock_transaction_page_renders_repeatable_stock_out_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItem = $this->createInventoryItems([10])[0];

        $response = $this->actingAs($user)->get(
            route('inventory.stock', $inventoryItem)
        );

        $response->assertOk();
        $response->assertSee('stockOutItemsBody');
        $response->assertSee('items[0][inventory_item_id]');
        $response->assertSee('Add Item');
        $response->assertSee('Used / Consumed');
    }

    public function test_stock_transaction_page_only_renders_the_selected_operation(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItem = $this->createInventoryItems([10])[0];

        $stockInResponse = $this->actingAs($user)->get(route('inventory.stock', [
            'inventoryItem' => $inventoryItem,
            'operation' => 'stock-in',
        ]));

        $stockInResponse->assertOk();
        $stockInResponse->assertSee('stockReceiptForm');
        $stockInResponse->assertDontSee('stockOutForm');

        $stockOutResponse = $this->get(route('inventory.stock', [
            'inventoryItem' => $inventoryItem,
            'operation' => 'stock-out',
        ]));

        $stockOutResponse->assertOk();
        $stockOutResponse->assertSee('stockOutForm');
        $stockOutResponse->assertDontSee('stockReceiptForm');
    }

    public function test_inventory_has_a_physical_count_shortcut_and_item_action(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItem = $this->createInventoryItems([10])[0];

        $response = $this->actingAs($user)->get(route('inventory.index', [
            'operation' => 'physical-count',
        ]));

        $response->assertOk();
        $response->assertSee('Physical Count');
        $response->assertSee('inventory-count-button active');
        $response->assertSee('aria-current="page"', false);
        $response->assertSee(route('inventory.stock', [
            'inventoryItem' => $inventoryItem,
            'operation' => 'physical-count',
        ]), false);
    }

    public function test_physical_count_mode_only_renders_the_adjustment_form(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);
        $inventoryItem = $this->createInventoryItems([10])[0];

        $response = $this->actingAs($user)->get(route('inventory.stock', [
            'inventoryItem' => $inventoryItem,
            'operation' => 'physical-count',
        ]));

        $response->assertOk();
        $response->assertSee('Actual Physical Count');
        $response->assertDontSee('stockReceiptForm');
        $response->assertDontSee('stockOutForm');
    }

    private function createInventoryItems(array $quantities): array
    {
        $now = now();
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Inventory',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $unitId = DB::table('units')->insertGetId([
            'name' => 'Each',
            'abbreviation' => 'ea',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return collect($quantities)
            ->values()
            ->map(fn ($quantity, $index) => InventoryItem::create([
                'category_id' => $categoryId,
                'unit_id' => $unitId,
                'name' => 'Item ' . ($index + 1),
                'sku' => 'TEST-' . ($index + 1),
                'quantity' => $quantity,
                'minimum_stock' => 0,
                'unit_cost' => 1,
                'is_active' => true,
            ]))
            ->all();
    }
}