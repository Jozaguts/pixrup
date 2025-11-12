<?php

use App\Jobs\ProcessGlowUpImageJob;
use App\Models\GlowupJob;
use App\Models\Property;
use App\Models\User;
use App\Models\UsagePropertyMonthly;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('authenticated users can create glowup jobs and enqueue processing', function (): void {
    Storage::fake('public');
    config(['glowup.disk' => 'public']);

    Bus::fake();

    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])->post(
        route('api.properties.glowup.jobs.store', ['property' => $property->id]),
        [
            'room_type' => 'living_room',
            'style' => 'modern',
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

    expect(UsagePropertyMonthly::query()->count())->toBe(1);
});

test('glowup job creation respects plan limits', function (): void {
    Storage::fake('public');
    config(['glowup.disk' => 'public']);
    config(['plans.tiers.professional.limit' => 0]);

    $user = User::factory()->create(['plan' => 'professional']);
    $property = Property::factory()->create();

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])->post(
        route('api.properties.glowup.jobs.store', ['property' => $property->id]),
        [
            'room_type' => 'living_room',
            'style' => 'modern',
            'image' => UploadedFile::fake()->image('room.jpg', 1200, 800),
        ],
    );

    $response->assertForbidden()
        ->assertJsonPath('message', 'Has alcanzado tu límite mensual de uso.')
        ->assertJsonStructure([
            'usage' => [
                'plan' => ['tier', 'label', 'limit'],
                'used',
                'remaining',
                'period_key',
                'resets_at',
            ],
        ]);
});

test('users can attach finished glowup jobs to the property', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create([
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
        route('api.glowup.jobs.attach', ['glowupJob' => $job->id]),
        [
            'action' => 'save_to_property',
        ],
    );

    $response->assertOk()->assertJsonPath('job.id', $job->id);

    $property->refresh();
    expect(data_get($property->metadata, 'glowup.attachments.0.job_id'))->toBe($job->id);
});
