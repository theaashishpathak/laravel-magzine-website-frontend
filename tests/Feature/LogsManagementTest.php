<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\EmailDeliveryLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class LogsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_access_email_delivery_logs_page(): void
    {
        $superAdmin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.logs.email.index'))
            ->assertOk()
            ->assertSee('Email Delivery Logs');
    }

    public function test_email_sending_and_sent_events_create_and_update_delivery_log(): void
    {
        config(['mail.default' => 'array']);

        Mail::raw('Hello Reader', function ($msg): void {
            $msg->to('reader@example.com')
                ->subject('Welcome to our magazine');
        });

        $this->assertDatabaseHas('email_delivery_logs', [
            'recipient_email' => 'reader@example.com',
            'subject' => 'Welcome to our magazine',
            'status' => EmailDeliveryLog::STATUS_DELIVERED,
        ]);

        $log = EmailDeliveryLog::where('recipient_email', 'reader@example.com')->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->sent_at);
    }

    public function test_email_delivery_log_can_be_filtered_and_inspected(): void
    {
        $superAdmin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $superAdmin->assignRole('Super Admin');

        EmailDeliveryLog::create([
            'recipient_email' => 'delivered@example.com',
            'subject' => 'Success Email',
            'mailer' => 'smtp',
            'status' => EmailDeliveryLog::STATUS_DELIVERED,
            'sent_at' => now(),
        ]);

        EmailDeliveryLog::create([
            'recipient_email' => 'failed@example.com',
            'subject' => 'Failed Email',
            'mailer' => 'smtp',
            'status' => EmailDeliveryLog::STATUS_FAILED,
            'error_message' => 'Connection to smtp.gmail.com timed out',
        ]);

        \Livewire\Livewire::actingAs($superAdmin)
            ->test(\App\Livewire\Admin\Logs\EmailLogIndex::class)
            ->set('status', 'failed')
            ->assertSee('failed@example.com')
            ->assertDontSee('delivered@example.com')
            ->call('inspect', EmailDeliveryLog::where('status', 'failed')->first()->id)
            ->assertSee('Connection to smtp.gmail.com timed out');
    }

    public function test_model_actions_are_recorded_in_activity_log(): void
    {
        $user = User::factory()->create([
            'name' => 'Editorial Lead',
            'email' => 'editorial@example.com',
        ]);

        $this->actingAs($user);

        $department = Department::create([
            'name' => 'Investigation Desk',
            'code' => 'INV-01',
            'status' => Department::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'department',
            'event' => 'created',
            'subject_id' => $department->id,
        ]);

        $superAdmin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.logs.activity.index'))
            ->assertOk()
            ->assertSee('Investigation Desk');
    }
}
