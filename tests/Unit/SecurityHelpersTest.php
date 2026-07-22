<?php

namespace Tests\Unit;

use App\Services\AnsibleService;
use App\Services\ProxmoxService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

class SecurityHelpersTest extends TestCase
{
    public function test_ansible_username_sanitizer_rejects_injection(): void
    {
        $svc = new class extends AnsibleService {
            public function __construct()
            {
                // skip parent (filesystem/ssh)
            }

            public function expose(?string $u): string
            {
                return $this->sanitizeVmUsername($u);
            }
        };

        $this->assertSame('student', $svc->expose("root; rm -rf /"));
        $this->assertSame('student', $svc->expose('admin$(id)'));
        $this->assertSame('lab_user', $svc->expose('lab_user'));
        $this->assertSame('student', $svc->expose(''));
    }

    public function test_proxmox_safe_segment_rejects_path_injection(): void
    {
        $svc = (new ReflectionClass(ProxmoxService::class))->newInstanceWithoutConstructor();
        $m = new ReflectionMethod(ProxmoxService::class, 'safeSegment');
        $m->setAccessible(true);

        $this->assertSame('pve', $m->invoke($svc, 'pve'));

        $this->expectException(RuntimeException::class);
        $m->invoke($svc, '../etc/passwd');
    }

    public function test_proxmox_safe_vmid_bounds(): void
    {
        $svc = (new ReflectionClass(ProxmoxService::class))->newInstanceWithoutConstructor();
        $m = new ReflectionMethod(ProxmoxService::class, 'safeVmid');
        $m->setAccessible(true);

        $this->assertSame(100, $m->invoke($svc, 100));

        $this->expectException(RuntimeException::class);
        $m->invoke($svc, 1);
    }
}
