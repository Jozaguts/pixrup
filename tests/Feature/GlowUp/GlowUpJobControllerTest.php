<?php

use App\Jobs\ProcessGlowUpImageJob;
use App\Models\GlowupJob;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('authenticated users can create glowup jobs and enqueue processing', function (): void {
    Storage::fake('public');
    config(['glowup.disk' => 'public']);

    Bus::fake();

    $user = User::factory()->create(['plan_tier' => 'PRICE_PRO']);
    $property = Property::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])->post(
        route('properties.glowup.jobs.store', ['property' => $property->id]),
        [
            'room_type' => 'living_room',
            'style' => 'modern',
            'prompt' => 'Transform the living room into a modern catalog-ready render.',
            'image' => UploadedFile::fake()->image('room.jpg', 1200, 800),
        ],
    );

    $response->assertCreated()->assertJsonPath('job.status', 'pending');

    $job = GlowupJob::query()->first();
    expect($job)->not->toBeNull();

    $this->assertDatabaseHas('glowup_jobs', [
        'property_id' => $property->id,
        'user_id' => $user->id,
        'room_type' => 'living_room',
        'style' => 'modern',
        'status' => 'pending',
    ]);

    Bus::assertDispatched(ProcessGlowUpImageJob::class, function (ProcessGlowUpImageJob $queued) use ($job): bool {
        return $queued->jobId === $job->id;
    });

    $user->refresh();
    expect($user->used_renders)->toBe(1);
});

test('glowup job creation respects plan limits', function (): void {
    Storage::fake('public');
    config(['glowup.disk' => 'public']);

    $user = User::factory()->create(['plan_tier' => 'PRICE_STARTER']);
    $property = Property::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])->post(
        route('properties.glowup.jobs.store', ['property' => $property->id]),
        [
            'room_type' => 'living_room',
            'style' => 'modern',
            'prompt' => 'Transform the living room into a modern catalog-ready render.',
            'image' => UploadedFile::fake()->image('room.jpg', 1200, 800),
        ],
    );

    $response->assertForbidden()
        ->assertJsonPath('message', 'You have reached your monthly property usage limit.')
        ->assertJsonPath('usage.bucket', 'renders')
        ->assertJsonPath('usage.plan.tier', 'PRICE_STARTER')
        ->assertJsonPath('usage.usage.renders.is_blocked', true);
});

test('users can attach finished glowup jobs to the property', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $user->id,
        'metadata' => [],
    ]);

    $job = GlowupJob::factory()
        ->done()
        ->create([
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])->post(
        route('glowup.jobs.attach', ['glowupJob' => $job->id]),
        [
            'action' => 'save_to_property',
        ],
    );

    $response->assertOk()->assertJsonPath('job.id', $job->id);

    $property->refresh();
    expect(data_get($property->metadata, 'glowup.attachments.0.job_id'))->toBe($job->id);
});
