<?php

namespace Tests\Feature\Folder;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;

class FolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_fetch_folders()
    {
        $this->getJson('api/v1/folders')->assertStatus(401);
    }

    public function test_guest_cannot_create_folder()
    {
        $this->postJson('api/v1/folders', [
            'name' => 'Folder name'
        ])->assertStatus(401);
    }

    public function test_guest_cannot_alter_folder()
    {
        $this->patchJson('api/v1/folders/' . RamseyUuid::uuid4()->toString(), [
            'name' => 'A new name'
        ])->assertStatus(401);
    }

    public function test_logged_user_can_create_folder()
    {
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        $this->postJson('api/v1/folders', [
            'name' => 'Folder name'
        ])->assertStatus(201);
    }

    public function test_logged_user_can_alter_folder()
    {
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        $folder = Folder::create([
            'name' => 'A folder name'
        ]);

        $this->patchJson('api/v1/folders/' . $folder->id, [
            'name' => 'New folder name'
        ])->assertStatus(200);
    }

    public function test_logged_user_can_delete_folder()
    {
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        $folder = Folder::factory()->create()->first();

        Document::factory(2)->create()->each(function ($document) use ($folder) {
            $document->folder_id = $folder->id;
            $document->save();
        });

        $this->assertTrue($folder->refresh()->documents()->count() > 0);
        $this->deleteJson('api/v1/folders/' . $folder->id)->assertStatus(200);
    }
}
