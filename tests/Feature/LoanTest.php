<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the loan creation.
     *
     * @return void
     */
    public function test_create_loan()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/loans', [
            'amount' => 5000,
            'interest_rate' => 5,
            'duration_years' => 2,
            'borrower_id' => User::factory()->create()->id,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Loan created successfully.',
                 ]);
    }

    /**
     * Test fetching all loans.
     *
     * @return void
     */
    public function test_get_all_loans()
    {
        Loan::factory(5)->create();

        $response = $this->getJson('/api/loans');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => ['id', 'amount', 'interest_rate', 'duration_years', 'lender_id', 'borrower_id']
                     ],
                 ]);
    }

    /**
     * Test updating a loan by the lender.
     *
     * @return void
     */
    public function test_update_loan()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['lender_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->patchJson("/api/loans/{$loan->id}", [
            'amount' => 6000,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Loan updated successfully.',
                     'data' => ['amount' => 6000]
                 ]);
    }

    /**
     * Test deleting a loan by the lender.
     *
     * @return void
     */
    public function test_delete_loan()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['lender_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/loans/{$loan->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Loan deleted successfully.',
                 ]);
    }
}
