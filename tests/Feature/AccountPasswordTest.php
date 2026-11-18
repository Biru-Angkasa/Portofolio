<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_or_submit_password_settings(): void
    {
        $this->get(route('admin.account.edit'))->assertRedirect(route('login'));
        $this->put(route('admin.account.update'), [])->assertRedirect(route('login'));
    }

    public function test_owner_can_open_password_settings(): void
    {
        $owner = User::factory()->create();
        $this->actingAs($owner)->get(route('admin.account.edit'))
            ->assertOk()
            ->assertSee($owner->email)
            ->assertSee('Kata sandi saat ini')
            ->assertSee('Simpan kata sandi');
    }

    /** @return array<string, array{array<string, string>, string, string}> */
    public static function invalidPasswords(): array
    {
        return [
            'wrong current password' => [['current_password' => 'wrong-password-12', 'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'], 'current_password', 'Kata sandi saat ini tidak sesuai.'],
            'short new password' => [['current_password' => 'old-password-123', 'password' => 'short-7', 'password_confirmation' => 'short-7'], 'password', 'Kata sandi baru minimal 8 karakter.'],
            'confirmation mismatch' => [['current_password' => 'old-password-123', 'password' => 'new-password-123', 'password_confirmation' => 'different-password'], 'password', 'Ulangi kata sandi baru dengan nilai yang sama.'],
            'missing new password' => [['current_password' => 'old-password-123'], 'password', 'Masukkan kata sandi baru.'],
        ];
    }

    /** @param array<string, string> $payload */
    #[DataProvider('invalidPasswords')]
    public function test_invalid_password_changes_preserve_the_existing_password(array $payload, string $field, string $message): void
    {
        $owner = User::factory()->create(['password' => 'old-password-123']);
        $originalHash = $owner->password;
        $this->actingAs($owner)
            ->from(route('admin.account.edit'))
            ->put(route('admin.account.update'), $payload)
            ->assertRedirect(route('admin.account.edit'))
            ->assertSessionHasErrors([$field => $message]);

        $this->assertSame($originalHash, $owner->fresh()->password);
        $this->get(route('admin.account.edit'))->assertDontSee('value="old-password-123"', false)->assertDontSee('value="new-password-123"', false);
    }

    public function test_password_change_hashes_the_new_password_keeps_the_owner_signed_in_and_does_not_change_other_accounts(): void
    {
        $owner = User::factory()->create(['password' => 'old-password-123', 'remember_token' => 'previous-token']);
        $other = User::factory()->create(['password' => 'other-password-123']);
        $otherHash = $other->password;
        $this->actingAs($owner)
            ->put(route('admin.account.update'), [
                'current_password' => 'old-password-123',
                'password' => 'new-pass',
                'password_confirmation' => 'new-pass',
                'email' => 'someone@example.com',
                'id' => $other->id,
            ])
            ->assertRedirect(route('admin.account.edit'))
            ->assertSessionHas('status', 'Kata sandi berhasil diubah.');

        $this->assertAuthenticatedAs($owner);
        $updated = $owner->fresh();
        $this->assertTrue(Hash::check('new-pass', $updated->password));
        $this->assertFalse(Hash::check('old-password-123', $updated->password));
        $this->assertNull($updated->remember_token);
        $this->assertSame($owner->email, $updated->email);
        $this->assertSame($otherHash, $other->fresh()->password);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->post(route('login'), ['email' => $owner->email, 'password' => 'old-password-123'])->assertSessionHasErrors('email');
        $this->post(route('login'), ['email' => $owner->email, 'password' => 'new-pass'])->assertRedirect(route('admin.works.index'));
        $this->assertAuthenticatedAs($owner);
    }
}
