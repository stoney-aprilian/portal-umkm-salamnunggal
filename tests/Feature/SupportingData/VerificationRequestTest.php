<?php

namespace Tests\Feature\SupportingData;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LogicException;
use Tests\TestCase;

class VerificationRequestTest extends TestCase
{
    use RefreshDatabase;

    private function umkmCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);
    }

    private function productCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
    }

    private function umkm(): Umkm
    {
        return Umkm::firstOrCreate(
            ['slug' => 'warung-nasi-bu-siti'],
            [
                'user_id' => User::factory()->create()->id,
                'category_id' => $this->umkmCategory()->id,
                'name' => 'Warung Nasi Bu Siti',
            ],
        );
    }

    private function product(): Product
    {
        return Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Nasi Uduk Komplit',
            'slug' => 'nasi-uduk-komplit',
            'price' => 15000,
        ]);
    }

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('verification_requests'));

        $columns = ['id', 'user_id', 'reviewer_id', 'verifiable_type', 'verifiable_id', 'status', 'notes', 'reviewed_at', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('verification_requests', $column), "missing column: {$column}");
        }
    }

    public function test_polymorphic_columns_are_indexed(): void
    {
        $this->assertTrue(Schema::hasIndex('verification_requests', 'verification_requests_verifiable_type_verifiable_id_index'));
    }

    public function test_no_direct_entity_foreign_key_columns_exist(): void
    {
        $this->assertFalse(Schema::hasColumn('verification_requests', 'umkm_id'));
        $this->assertFalse(Schema::hasColumn('verification_requests', 'product_id'));
    }

    public function test_no_unrelated_fields_were_invented(): void
    {
        $columns = ['reviewed_by', 'action', 'type', 'decision', 'approved_at', 'rejected_at'];

        foreach ($columns as $column) {
            $this->assertFalse(Schema::hasColumn('verification_requests', $column), "unexpected column: {$column}");
        }
    }

    public function test_request_can_belong_to_an_umkm(): void
    {
        $umkm = $this->umkm();
        $user = User::factory()->create();

        $request = $umkm->verificationRequests()->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $umkm->id,
        ]);
    }

    public function test_request_can_belong_to_a_product(): void
    {
        $product = $this->product();
        $user = User::factory()->create();

        $request = $product->verificationRequests()->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'verifiable_type' => Product::class,
            'verifiable_id' => $product->id,
        ]);
    }

    public function test_verifiable_resolves_to_the_correct_umkm(): void
    {
        $umkm = $this->umkm();

        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $umkm->id,
        ]);

        $this->assertInstanceOf(Umkm::class, $request->verifiable);
        $this->assertSame($umkm->id, $request->verifiable->id);
    }

    public function test_verifiable_resolves_to_the_correct_product(): void
    {
        $product = $this->product();

        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Product::class,
            'verifiable_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $request->verifiable);
        $this->assertSame($product->id, $request->verifiable->id);
    }

    public function test_umkm_verification_requests_relation_resolves(): void
    {
        $umkm = $this->umkm();
        $user = User::factory()->create();

        $request = $umkm->verificationRequests()->create(['user_id' => $user->id]);

        $this->assertCount(1, $umkm->verificationRequests);
        $this->assertTrue($umkm->verificationRequests->contains($request));
    }

    public function test_product_verification_requests_relation_resolves(): void
    {
        $product = $this->product();
        $user = User::factory()->create();

        $request = $product->verificationRequests()->create(['user_id' => $user->id]);

        $this->assertCount(1, $product->verificationRequests);
        $this->assertTrue($product->verificationRequests->contains($request));
    }

    public function test_user_relationship_resolves_to_submitting_user(): void
    {
        $user = User::factory()->create();

        $request = VerificationRequest::create([
            'user_id' => $user->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $this->assertSame($user->id, $request->user->id);
    }

    public function test_reviewer_relationship_resolves_to_reviewing_user(): void
    {
        $reviewer = User::factory()->create();

        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'reviewer_id' => $reviewer->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
            'status' => 'approved',
            'notes' => 'Data lengkap.',
            'reviewed_at' => now(),
        ]);

        $this->assertSame($reviewer->id, $request->reviewer->id);
    }

    public function test_reviewer_can_be_null_before_review(): void
    {
        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $this->assertNull($request->fresh()->reviewer_id);
    }

    public function test_reviewed_at_can_be_null_before_review(): void
    {
        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $this->assertNull($request->fresh()->reviewed_at);
    }

    public function test_notes_can_be_null(): void
    {
        $request = VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $this->assertNull($request->fresh()->notes);
    }

    public function test_all_documented_status_values_are_valid(): void
    {
        $statuses = ['pending', 'approved', 'needs_revision', 'rejected'];

        foreach ($statuses as $status) {
            VerificationRequest::create([
                'user_id' => User::factory()->create()->id,
                'verifiable_type' => Umkm::class,
                'verifiable_id' => $this->umkm()->id,
                'status' => $status,
            ]);
        }

        foreach ($statuses as $status) {
            $this->assertDatabaseHas('verification_requests', ['status' => $status]);
        }
    }

    public function test_status_defaults_to_pending(): void
    {
        VerificationRequest::create([
            'user_id' => User::factory()->create()->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $fresh = VerificationRequest::first();

        $this->assertSame('pending', $fresh->status);
    }

    public function test_entity_draft_status_is_rejected_for_requests(): void
    {
        $this->assertThrows(
            fn () => DB::table('verification_requests')->insert([
                'user_id' => User::factory()->create()->id,
                'verifiable_type' => Umkm::class,
                'verifiable_id' => $this->umkm()->id,
                'status' => 'draft',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_unsupported_target_is_rejected(): void
    {
        $this->assertThrows(
            fn () => VerificationRequest::create([
                'user_id' => User::factory()->create()->id,
                'verifiable_type' => User::class,
                'verifiable_id' => User::factory()->create()->id,
            ]),
            LogicException::class,
        );

        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_deleting_submitting_user_with_requests_is_blocked(): void
    {
        $user = User::factory()->create();

        VerificationRequest::create([
            'user_id' => $user->id,
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $this->umkm()->id,
        ]);

        $this->assertThrows(
            fn () => $user->delete(),
            QueryException::class,
        );

        $this->assertDatabaseCount('verification_requests', 1);
    }
}
