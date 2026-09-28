<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\FormSubmission;
use Statamic\Facades\Stache;
use Tests\TestCase;

class MeridianKitTest extends TestCase
{
    public function test_homepage_renders_meridian_content_and_native_navigation(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk()->assertSeeText('Ready to perform.');
        $response->assertSee('href="/services"', false);
        $response->assertSee('<title>Home — Meridian</title>', false);
        $response->assertDontSee('webflow.js');
    }

    #[DataProvider('contentRoutes')]
    public function test_content_route_renders_its_heading(string $route, string $heading): void
    {
        $this->withoutVite();

        $this->get($route)->assertOk()->assertSeeText($heading);
    }

    /** @return array<string, array{string, string}> */
    public static function contentRoutes(): array
    {
        return [
            'services' => ['/services', 'Services'],
            'case studies' => ['/case-studies', 'Progress'],
            'pricing' => ['/pricing', 'Pricing'],
            'insights' => ['/blog', 'Insights'],
            'about' => ['/about', 'Meridian'],
            'contact' => ['/contact', 'next right move.'],
            'operations audit' => ['/services/operations-audit', 'Operations Audit'],
            'systems design' => ['/services/systems-design', 'Systems Design'],
            'alignment' => ['/services/organizational-alignment', 'Organizational Alignment'],
            'manufacturing case' => ['/case-studies/manufacturing-fulfillment', 'Reducing fulfillment friction'],
            'healthcare case' => ['/case-studies/healthcare-onboarding', 'A calmer path through onboarding'],
            'distribution case' => ['/case-studies/distribution-capacity', 'Capacity without constant escalation'],
            'growth insight' => ['/blog/slow-down-before-growth', 'The case for slowing down'],
            'operations insight' => ['/blog/operations-audit-without-disruption', 'How to review operations'],
        ];
    }

    public function test_contact_page_renders_a_native_form(): void
    {
        $this->withoutVite();

        $response = $this->get('/contact');

        $response->assertOk()->assertSee('method="POST"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="service"', false);
        $response->assertSee('name="_token"', false);
    }

    public function test_invalid_discovery_enquiry_returns_validation_errors(): void
    {
        $this->postJson('/!/forms/discovery', [
            'email' => 'invalid-email',
            'message' => 'short',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_valid_discovery_enquiry_is_stored_and_honeypot_is_discarded(): void
    {
        Mail::fake();
        $submissionDirectory = sys_get_temp_dir().'/meridian-forms-'.bin2hex(random_bytes(8));
        Stache::store('form-submissions')->directory($submissionDirectory.'/');

        try {
            $details = [
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'message' => 'We would like to improve how our teams coordinate projects.',
            ];

            $this->postJson('/!/forms/discovery', $details)
                ->assertOk()->assertJson(['success' => true, 'submission_created' => true]);

            $submissions = FormSubmission::whereForm('discovery');
            $this->assertCount(1, $submissions);
            $this->assertSame($details['email'], $submissions->first()->get('email'));
            $this->assertFileExists($submissions->first()->path());

            $this->postJson('/!/forms/discovery', [...$details, 'website' => 'spam.example.com'])
                ->assertOk()->assertJson(['success' => true, 'submission_created' => false]);

            $this->assertCount(1, FormSubmission::whereForm('discovery'));
            Mail::assertNothingSent();
        } finally {
            File::deleteDirectory($submissionDirectory);
        }
    }

    public function test_missing_page_renders_a_custom_404(): void
    {
        $this->withoutVite();

        $this->get('/missing-meridian-page')->assertNotFound()->assertSeeText('This page has moved on.');
    }
}
