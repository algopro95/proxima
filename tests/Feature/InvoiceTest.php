<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

public function test_user_can_create_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/invoices', [
            'due_date' => '2024-06-01',
            'items' => [
                ['item_name' => 'Item 1', 'qty' => 2, 'price' => 100],
                ['item_name' => 'Item 2', 'qty' => 1, 'price' => 200],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'user_id', 'invoice_number', 'due_date', 'total'
            ]);
    }

    public function test_user_can_update_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $invoice = Invoice::factory()->create(['user_id' => $user->id]);
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'item_name' => 'Old Item',
            'qty' => 1,
            'price' => 100,
        ]);

        $response = $this->putJson("/api/invoices/{$invoice->id}", [
            'due_date' => '2024-07-01',
            'items' => [
                ['item_name' => 'Updated Item 1', 'qty' => 2, 'price' => 150],
                ['item_name' => 'Updated Item 2', 'qty' => 3, 'price' => 100],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['due_date' => '2024-07-01']);
            // ->assertJsonFragment(['item_name' => 'Updated Item 1'])
            // ->assertJsonFragment(['item_name' => 'Updated Item 2']);
    }

    public function test_user_can_delete_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $invoice = Invoice::factory()->create(['user_id' => $user->id]);
        InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('invoice_items', ['invoice_id' => $invoice->id]);
    }

    public function test_user_can_list_invoices()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Invoice::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links']);
    }

    public function test_user_can_view_invoice_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $invoice = Invoice::factory()->create(['user_id' => $user->id]);
        InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'item_name' => 'Test Item', 'qty' => 1, 'price' => 100]);

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['item_name' => 'Test Item']);
    }
}
