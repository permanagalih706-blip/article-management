<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_user_can_create_article_with_cover_and_media()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $coverFile = UploadedFile::fake()->image('cover.jpg');
        $mediaFile1 = UploadedFile::fake()->image('photo.jpg');
        $mediaFile2 = UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4');

        $response = $this->post('/articles', [
            'title' => 'My Test Article',
            'content' => 'Lorem ipsum dolor sit amet.',
            'status' => 'published',
            'cover_image' => $coverFile,
            'media' => [$mediaFile1, $mediaFile2],
        ]);

        $response->assertRedirect('/dashboard');

        $article = Article::first();
        $this->assertNotNull($article);
        $this->assertEquals('My Test Article', $article->title);
        $this->assertNotNull($article->cover_image);

        // Verify files exist in fake storage
        Storage::disk('public')->assertExists($article->cover_image);
        
        $this->assertEquals(2, $article->media()->count());
        foreach ($article->media as $media) {
            Storage::disk('public')->assertExists($media->file_path);
        }
    }

    public function test_scheduled_articles_are_not_visible_on_public_list()
    {
        $user = User::factory()->create();

        // Article published in the past (visible)
        Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Past Article',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        // Article scheduled in the future (hidden)
        Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Future Article',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        // Article as draft (hidden)
        Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Draft Article',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('/articles');
        $response->assertStatus(200);
        $response->assertSee('Past Article');
        $response->assertDontSee('Future Article');
        $response->assertDontSee('Draft Article');
    }

    public function test_unauthorized_user_cannot_delete_media()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $article = Article::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Owner Article',
            'status' => 'published',
        ]);

        $media = Media::create([
            'article_id' => $article->id,
            'type' => 'image',
            'file_path' => 'media/photo.jpg',
            'created_by' => $owner->id,
        ]);

        // Non-logged in gets redirected to login (due to auth middleware)
        $response = $this->delete("/media/{$media->id}");
        $response->assertRedirect('/login');

        // Other user gets 403
        $this->actingAs($otherUser);
        $response = $this->delete("/media/{$media->id}");
        $response->assertStatus(403);

        // Owner can delete
        $this->actingAs($owner);
        $response = $this->delete("/media/{$media->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
