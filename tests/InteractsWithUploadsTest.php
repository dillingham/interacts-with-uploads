<?php

namespace Dillingham\InteractsWithUploads\Tests;

use Dillingham\InteractsWithUploads\Tests\Fixtures\CreatePostRequest;
use Dillingham\InteractsWithUploads\Tests\Fixtures\Post;
use Dillingham\InteractsWithUploads\Tests\Fixtures\UpdatePostRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class InteractsWithUploadsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::post('/posts/new', function (CreatePostRequest $request) {

            $request->upload('image');

            return Post::create($request->validated());
        });

        Route::put('/posts/{post}/edit', function (Post $post, UpdatePostRequest $request) {

            $request->upload('post.image');

            return $post->update($request->validated());
        })->middleware('bindings');
    }

    public function test_creating_file_via_request()
    {
        Storage::fake('public');

        $this->post('/posts/new', [
            'title' => 'Hello',
            'body' => 'World',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertNotNull(Post::first()->image);

        Storage::disk('public')->assertExists(Post::first()->image);
    }

    public function test_updating_file_via_request()
    {
        Storage::fake('public');

        Storage::put('images/original.jpg', '123');

        $post = Post::create([
            'title' => 'hello',
            'body' => 'world',
            'image' => 'images/original.jpg',
        ]);

        $this->put("posts/{$post->id}/edit", [
            'title' => 'updated',
            'body' => 'updated',
            'image' => UploadedFile::fake()->image('updated.jpg'),
        ]);

        $post = $post->fresh();
        $this->assertNotNull($post->image);
        $this->assertNotEquals('images/original.jpg', $post->image);
        Storage::disk('public')->assertExists($post->image);
        Storage::disk('public')->assertMissing('images/original.jpg');
    }

    public function test_updating_with_null_keeps_original()
    {
        Storage::fake('public');

        Storage::disk('public')->put('images/original.jpg', '123');

        $post = Post::create([
            'title' => 'hello',
            'body' => 'world',
            'image' => 'images/original.jpg',
        ]);

        $this->put("posts/{$post->id}/edit", [
            'title' => 'updated',
            'body' => 'updated',
            'image' => null,
        ]);

        $post = $post->fresh();
        $this->assertEquals('images/original.jpg', $post->image);
        Storage::disk('public')->assertExists('images/original.jpg');
    }
}

