<?php

namespace Tests\Feature\Document;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_user_can_delete_document()
    {
        $user = User::factory()->create()->first();

        $this->actingAs($user);

        $document = Document::factory()->create([
            'user_id' => $user->id
        ])->first();

        $this->deleteJson('api/v1/documents/' . $document->id)
             ->assertStatus(200)
        ;
    }

    public function test_logged_user_can_only_delete_its_own_document()
    {
        $user = User::factory()->create();
        $user = User::where('email', $user->email)->first();
        $user->documents()->save(Document::factory()->create());

        $user2 = User::factory()->create();
        $user2 = User::where('email', $user2->email)->first();
        $user2->documents()->save(Document::factory()->create());

        $this->actingAs($user);

        $this->deleteJson('api/v1/documents/' . $user->documents()->first()->id)
            ->assertStatus(200)
        ;

        $this->deleteJson('api/v1/documents/' . $user2->documents()->first()->id)
             ->assertStatus(404)
        ;
    }
}
