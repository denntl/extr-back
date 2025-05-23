<?php

namespace Tests\Unit;

use App\Enums\Authorization\PermissionName;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    private array $validPrefixes = ['Manage', 'Client','Common'];
    private array $validSuffixes = ['Create', 'Read', 'Update', 'Delete', 'Clone'];
    private array $exceptionalSuffixes = [
        'Save',
        'SendMessage',
        'Invite',
        'Activate',
        'Deactivate',
        'LoginAsUser',
        'ManualBalanceDeposit',
    ];

    public function testEnumKeysStartWithValidPrefix()
    {
        foreach (PermissionName::cases() as $case) {
            $this->assertTrue(
                $this->startsWithValidPrefix($case->name),
                "{$case->name} does not start with any of the valid prefixes"
            );
        }
    }

    public function testEnumKeysEndWithValidSuffix()
    {
        foreach (PermissionName::cases() as $case) {
            $this->assertTrue(
                $this->endsWithValidSuffix($case->name) || $this->isExceptionalSuffix($case->name),
                "{$case->name} does not end with any of the valid suffixes or exceptional suffixes"
            );
        }
    }

    public function testPermissionNameMatchesWithValue()
    {
        foreach (PermissionName::cases() as $case) {
            $this->assertTrue(
                $case->name === ucfirst($case->value),
                "{$case->name} does not end with any of the valid suffixes or exceptional suffixes"
            );
        }
    }

    private function startsWithValidPrefix(string $string): bool
    {
        foreach ($this->validPrefixes as $prefix) {
            if (str_starts_with($string, $prefix)) {
                return true;
            }
        }
        return false;
    }
    private function endsWithValidSuffix(string $string): bool
    {
        foreach ($this->validSuffixes as $suffix) {
            if (str_ends_with($string, $suffix)) {
                return true;
            }
        }
        return false;
    }

    private function isExceptionalSuffix(string $string): bool
    {
        foreach ($this->exceptionalSuffixes as $suffix) {
            if (str_ends_with($string, $suffix)) {
                return true;
            }
        }
        return false;
    }
}
