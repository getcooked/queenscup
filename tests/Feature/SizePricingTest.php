<?php
namespace Tests\Feature;
use App\Models\Inventory; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class SizePricingTest extends TestCase {
    use RefreshDatabase;
    public function test_sizes_are_priced_differently()
    {
        $d = Inventory::create(['name'=>'Wintermelon','category'=>'Milktea','regular_price'=>79,'large_price'=>99,'stock'=>50]);
        $r = $this->postJson('/api/v1/reservations', ['service_type'=>'dine_in','customer_name'=>'A',
            'items'=>[['inventory_id'=>$d->id,'size'=>'regular','quantity'=>1],['inventory_id'=>$d->id,'size'=>'large','quantity'=>1]]])->assertCreated();
        $items = collect($r->json('items'))->keyBy('size_label');
        $this->assertSame(79.0, (float) $items['16oz']['unit_price']);
        $this->assertSame(99.0, (float) $items['22oz']['unit_price']);
    }
    public function test_a_large_with_no_large_price_must_not_sell_for_nothing()
    {
        // Products that only come in one size leave large_price at its 0 default.
        $d = Inventory::create(['name'=>'Espresso','category'=>'Coffee','regular_price'=>90,'large_price'=>0,'stock'=>50]);
        $this->postJson('/api/v1/reservations', ['service_type'=>'dine_in','customer_name'=>'A',
            'items'=>[['inventory_id'=>$d->id,'size'=>'large','quantity'=>2]]])->assertStatus(422);
    }
}
