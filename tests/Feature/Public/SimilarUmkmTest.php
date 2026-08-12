<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimilarUmkmTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);
    }

    private function otherCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => 'Kerajinan',
            'slug' => 'kerajinan',
        ]);
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function umkmFor(Category $category, string $status = 'approved', string $name = 'Warung Maju'): Umkm
    {
        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Umkm::generateUniqueSlug($name),
            'status' => $status,
        ]);
    }

    public function test_similar_umkm_share_the_same_category(): void
    {
        $this->umkmFor($this->category(), 'approved', 'Kedai Kopi Senja');
        $this->umkmFor($this->category(), 'approved', 'Warung Makan Bu Siti');
        $this->umkmFor($this->category(), 'approved', 'Katering Sehat');

        $this->get(route('public.umkm.show', 'kedai-kopi-senja'))
            ->assertOk()
            ->assertSee('UMKM Serupa')
            ->assertSee('Warung Makan Bu Siti')
            ->assertSee('Katering Sehat');
    }

    public function test_similar_umkm_exclude_non_approved(): void
    {
        $this->umkmFor($this->category(), 'approved', 'Kedai Kopi Senja');
        $this->umkmFor($this->category(), 'draft', 'Warung Draft');
        $this->umkmFor($this->category(), 'pending', 'Warung Pending');
        $this->umkmFor($this->category(), 'rejected', 'Warung Ditolak');

        $this->get(route('public.umkm.show', 'kedai-kopi-senja'))
            ->assertOk()
            ->assertDontSee('UMKM Serupa')
            ->assertDontSee('Warung Draft')
            ->assertDontSee('Warung Pending')
            ->assertDontSee('Warung Ditolak');
    }

    public function test_similar_umkm_exclude_other_categories(): void
    {
        $this->umkmFor($this->category(), 'approved', 'Kedai Kopi Senja');
        $this->umkmFor($this->otherCategory(), 'approved', 'Pengrajin Anyaman');

        $this->get(route('public.umkm.show', 'kedai-kopi-senja'))
            ->assertOk()
            ->assertDontSee('UMKM Serupa')
            ->assertDontSee('Pengrajin Anyaman');
    }

    public function test_similar_umkm_section_hidden_when_empty(): void
    {
        $this->umkmFor($this->category(), 'approved', 'Kedai Kopi Senja');

        $this->get(route('public.umkm.show', 'kedai-kopi-senja'))
            ->assertOk()
            ->assertDontSee('UMKM Serupa');
    }

    public function test_similar_umkm_are_limited_to_four(): void
    {
        $this->umkmFor($this->category(), 'approved', 'Kedai Kopi Senja');

        foreach (['Warung A', 'Warung B', 'Warung C', 'Warung D', 'Warung E', 'Warung F'] as $name) {
            $this->umkmFor($this->category(), 'approved', $name);
        }

        $response = $this->get(route('public.umkm.show', 'kedai-kopi-senja'));
        $response->assertOk()->assertSee('UMKM Serupa');

        $this->assertSame(4, substr_count($response->getContent(), 'Lihat Detail'));
    }
}
