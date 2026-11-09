<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminIpWhitelistTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_allowed_when_whitelist_is_empty(): void
    {
        config(['admin.ip_whitelist' => []]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
            ->get(route('admin.login'));

        $response->assertStatus(200);
    }

    public function test_admin_access_allowed_when_client_ip_is_whitelisted(): void
    {
        config([
            'admin.ip_whitelist' => ['192.168.1.50', '10.0.0.1'],
            'admin.always_allow_local' => false,
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.50'])
            ->get(route('admin.login'));

        $response->assertStatus(200);
    }

    public function test_admin_access_forbidden_when_client_ip_is_not_whitelisted(): void
    {
        config([
            'admin.ip_whitelist' => ['192.168.1.50'],
            'admin.always_allow_local' => false,
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.19'])
            ->get(route('admin.login'));

        $response->assertStatus(403);
    }

    public function test_admin_access_supports_cidr_subnet_notation(): void
    {
        config([
            'admin.ip_whitelist' => ['192.168.1.0/24'],
            'admin.always_allow_local' => false,
        ]);

        // IP di dalam subnet 192.168.1.0/24 -> diizinkan
        $allowedResponse = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.88'])
            ->get(route('admin.login'));
        $allowedResponse->assertStatus(200);

        // IP di luar subnet 192.168.1.0/24 -> diblokir 403
        $blockedResponse = $this->withServerVariables(['REMOTE_ADDR' => '192.168.2.88'])
            ->get(route('admin.login'));
        $blockedResponse->assertStatus(403);
    }

    public function test_admin_access_allowed_for_localhost_when_always_allow_local_is_true(): void
    {
        config([
            'admin.ip_whitelist' => ['10.0.0.5'],
            'admin.always_allow_local' => true,
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get(route('admin.login'));

        $response->assertStatus(200);
    }
}
