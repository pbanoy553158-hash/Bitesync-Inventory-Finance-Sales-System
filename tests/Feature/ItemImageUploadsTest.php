<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemImageUploadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_image_files_use_the_item_fallback_instead_of_broken_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'CEO/Admin']);
        [$inventoryCategoryId, $unitId] = $this->createInventoryCategoryAndUnit();
        $productCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Products',
            'description' => null,
            'type' => 'product',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = Product::create([
            'category_id' => $productCategoryId,
            'name' => 'Missing Photo Burger',
            'sku' => 'MISSING-IMAGE-001',
            'selling_price' => 10,
            'image' => 'products/missing-burger.webp',
            'is_active' => true,
        ]);
        $inventoryItem = InventoryItem::create([
            'category_id' => $inventoryCategoryId,
            'unit_id' => $unitId,
            'name' => 'Missing Photo Ingredient',
            'sku' => 'MISSING-IMAGE-002',
            'quantity' => 1,
            'minimum_stock' => 0,
            'unit_cost' => 1,
            'image' => 'inventory-items/missing-ingredient.webp',
            'is_active' => true,
        ]);

        $this->assertNull($product->image_url);
        $this->assertNull($inventoryItem->image_url);

        $this->actingAs($admin)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Missing Photo Burger')
            ->assertSee('products-fallback-icon')
            ->assertDontSee('missing-burger.webp');
    }

    public function test_inventory_item_images_can_be_uploaded_and_replaced(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'CEO/Admin']);
        [$categoryId, $unitId] = $this->createInventoryCategoryAndUnit();

        $this->actingAs($admin)->post(route('inventory.store'), [
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'name' => 'Chicken Breast',
            'sku' => 'CHICKEN-001',
            'quantity' => 5,
            'minimum_stock' => 1,
            'maximum_stock' => 20,
            'unit_cost' => 10,
            'location' => 'Freezer',
            'description' => 'Fresh chicken breast',
            'image' => $this->fakeImage('chicken.png'),
        ])->assertRedirect(route('inventory.index'));

        $item = InventoryItem::query()->firstOrFail();
        $originalImage = $item->image;

        $this->assertNotNull($originalImage);
        Storage::disk('public')->assertExists($originalImage);

        $this->actingAs($admin)->put(route('inventory.update', $item), [
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'name' => 'Chicken Breast',
            'sku' => 'CHICKEN-001',
            'minimum_stock' => 1,
            'maximum_stock' => 20,
            'unit_cost' => 10,
            'location' => 'Freezer',
            'description' => 'Fresh chicken breast',
            'is_active' => 1,
            'image' => $this->fakeImage('chicken-new.png'),
        ])->assertRedirect(route('inventory.index'));

        $item->refresh();
        $this->assertNotSame($originalImage, $item->image);
        Storage::disk('public')->assertExists($item->image);
        Storage::disk('public')->assertMissing($originalImage);

        $this->actingAs($admin)
            ->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('storage/' . $item->image);
    }

    public function test_product_edit_page_allows_replacing_the_product_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'CEO/Admin']);
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Products',
            'description' => null,
            'type' => 'product',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $categoryId,
            'name' => 'Test Burger',
            'sku' => 'BURGER-001',
            'selling_price' => 10,
            'description' => '',
            'image' => $this->fakeImage('burger.png'),
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->firstOrFail();
        $originalImage = $product->image;
        $this->assertNotNull($originalImage);
        Storage::disk('public')->assertExists($originalImage);

        $this->actingAs($admin)
            ->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Product Photo')
            ->assertSee('storage/' . $originalImage);

        $this->actingAs($admin)->put(route('products.update', $product), [
            'category_id' => $categoryId,
            'name' => 'Test Burger',
            'sku' => 'BURGER-001',
            'selling_price' => 10,
            'description' => '',
            'is_active' => 1,
            'image' => $this->fakeImage('burger-new.png'),
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertNotSame($originalImage, $product->image);
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing($originalImage);

        $this->actingAs($admin)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('storage/' . $product->image);
    }

    private function createInventoryCategoryAndUnit(): array
    {
        $now = now();
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Inventory',
            'description' => null,
            'type' => 'inventory',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $unitId = DB::table('units')->insertGetId([
            'name' => 'Each',
            'abbreviation' => 'ea',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return [$categoryId, $unitId];
    }

    private function fakeImage(string $name): UploadedFile
    {
        $contents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'
        );

        return UploadedFile::fake()->createWithContent($name, $contents);
    }
}
