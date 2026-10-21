<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Identity\Enums\UserRole;
use App\Modules\Identity\Enums\VerificationStatus;
use App\Modules\Identity\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('local');
});

it('completes the whole farmer flow from registration to product creation', function () {
    $this->post('/register', [
        'name' => 'Rosa Mamani',
        'email' => 'rosa@example.com',
        'password' => 'secreto123',
        'password_confirmation' => 'secreto123',
    ])->assertRedirect(route('onboarding.welcome'));

    $farmer = User::where('email', 'rosa@example.com')->sole();

    // The onboarding waits until the e-mail is verified with the link we sent.
    $this->get(route('onboarding.welcome'))->assertRedirect(route('verification.notice'));
    $this->get(URL::temporarySignedRoute('verification.verify', now()->addHour(), [
        'id' => $farmer->id,
        'hash' => sha1($farmer->email),
    ]));
    expect($farmer->fresh()->hasVerifiedEmail())->toBeTrue();

    $this->post(route('onboarding.role'), ['role' => 'farmer'])->assertRedirect(route('onboarding.farmer'));
    expect($farmer->fresh()->isFarmer())->toBeTrue();

    $this->post(route('onboarding.verification'), [
        'face_photo' => UploadedFile::fake()->image('selfie.jpg'),
        'dni_front' => UploadedFile::fake()->image('front.jpg'),
        'dni_back' => UploadedFile::fake()->image('back.jpg'),
    ])->assertRedirect(route('farmer.dashboard'));

    expect($farmer->fresh()->verification_status)->toBe(VerificationStatus::Pending);
    $this->get(route('farmer.dashboard'))->assertSuccessful()->assertViewIs('farmer.pending');

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.verify', [$farmer, VerificationStatus::Approved]))
        ->assertRedirect(route('admin.users'));

    $farmer->refresh();
    expect($farmer->isVerified())->toBeTrue();

    Category::factory()->create();

    $this->actingAs($farmer)
        ->get(route('dashboard.productos.create'))
        ->assertSuccessful();
});

it('lets a buyer finish the onboarding', function () {
    $user = User::factory()->pendingOnboarding()->create();

    $this->actingAs($user)->post(route('onboarding.role'), ['role' => 'user'])
        ->assertRedirect(route('onboarding.user'));
    $this->post(route('onboarding.preferences'))->assertRedirect(route('home'));

    expect($user->fresh())
        ->role->toBe(UserRole::User)
        ->onboarding_completed->toBeTruthy();
});

it('does not allow choosing the admin role', function () {
    $user = User::factory()->pendingOnboarding()->create();

    $this->actingAs($user)->post(route('onboarding.role'), ['role' => 'admin'])
        ->assertSessionHasErrors('role');

    expect($user->fresh()->isAdmin())->toBeFalse();
});

it('does not allow changing the role after the onboarding', function () {
    $buyer = User::factory()->create();

    $this->actingAs($buyer)->post(route('onboarding.role'), ['role' => 'farmer'])
        ->assertRedirect(route('home'));

    expect($buyer->fresh()->isFarmer())->toBeFalse();
});

it('only accepts verification documents from farmers', function () {
    $buyer = User::factory()->pendingOnboarding()->create();

    $this->actingAs($buyer)->post(route('onboarding.verification'), [
        'face_photo' => UploadedFile::fake()->image('selfie.jpg'),
        'dni_front' => UploadedFile::fake()->image('front.jpg'),
        'dni_back' => UploadedFile::fake()->image('back.jpg'),
    ])->assertForbidden();
});

it('only verifies farmer accounts', function () {
    $buyer = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.verify', [$buyer, VerificationStatus::Approved]));

    expect($buyer->fresh()->isVerified())->toBeFalse();
});

it('logs out banned users on any page', function () {
    $user = User::factory()->banned()->create();

    $this->actingAs($user)->get(route('orders.index'))->assertRedirect(route('login'));

    $this->assertGuest();
});

it('does not let banned users log in with a password', function () {
    $user = User::factory()->banned()->create(['password' => 'secreto123']);

    $this->post('/login', ['email' => $user->email, 'password' => 'secreto123'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('does not let admins ban themselves or other admins', function () {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.ban', $admin));
    $this->actingAs($admin)->post(route('admin.ban', $otherAdmin));

    expect($admin->fresh()->is_banned)->toBeFalse()
        ->and($otherAdmin->fresh()->is_banned)->toBeFalse();
});

it('lets admins ban regular users', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.ban', $user));

    expect($user->fresh()->is_banned)->toBeTrue();
});

it('drops the legacy is_admin column', function () {
    expect(Schema::hasColumn('users', 'is_admin'))->toBeFalse();
});
