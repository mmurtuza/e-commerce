<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\BdPhone;
use PHPUnit\Framework\TestCase;

class BdPhoneTest extends TestCase
{
    public function test_valid_bangladeshi_phone_numbers_pass(): void
    {
        $rule = new BdPhone;
        $failed = false;

        $fail = function (string $message) use (&$failed): void {
            $failed = true;
        };

        $validNumbers = [
            '01712345678',
            '01812345678',
            '01912345678',
            '01312345678',
            '01412345678',
            '01512345678',
            '01612345678',
            '+8801712345678',
            '008801812345678',
            '8801912345678',
        ];

        foreach ($validNumbers as $number) {
            $failed = false;
            $rule->validate('phone', $number, $fail);
            $this->assertFalse($failed, "Phone number {$number} should be valid.");
        }
    }

    public function test_invalid_phone_numbers_fail_validation(): void
    {
        $rule = new BdPhone;
        $invalidNumbers = [
            '01212345678', // Invalid operator code '12'
            '0171234567',  // Too short
            '017123456789', // Too long
            '12345678901',
            'abcdefghijk',
            '',
        ];

        foreach ($invalidNumbers as $number) {
            $failedMessage = null;
            $fail = function (string $message) use (&$failedMessage): void {
                $failedMessage = $message;
            };

            $rule->validate('phone', $number, $fail);
            $this->assertNotNull($failedMessage, "Phone number '{$number}' should fail validation.");
            $this->assertSame('The :attribute must be a valid Bangladeshi phone number.', $failedMessage);
        }
    }
}
