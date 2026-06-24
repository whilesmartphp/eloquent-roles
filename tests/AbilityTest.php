<?php

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Models\Ability;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;
use Workbench\App\Models\Workspace;

use function Orchestra\Testbench\workbench_path;

#[WithMigration]
class AbilityTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'email' => Factory::create()->unique()->safeEmail,
            'name' => Factory::create()->unique()->name,
            'password' => 'password',
        ], $attributes));
    }

    protected function createPost(array $attributes = []): Post
    {
        return Post::create(array_merge([
            'title' => Factory::create()->sentence,
            'status' => 'draft',
        ], $attributes));
    }

    protected function createWorkspace(array $attributes = []): Workspace
    {
        return Workspace::create(array_merge([
            'name' => Factory::create()->company,
        ], $attributes));
    }

    public function test_user_can_grant_ability()
    {
        $user = $this->createUser();

        $user->grantAbility('create', Post::class);

        $this->assertDatabaseHas('abilities', [
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
            'action' => 'create',
            'subject_type' => Post::class,
            'allowed' => true,
        ]);
    }

    public function test_user_can_check_ability()
    {
        $user = $this->createUser();
        $user->grantAbility('create', Post::class);

        $this->assertTrue($user->hasAbility('create', Post::class));
        $this->assertFalse($user->hasAbility('delete', Post::class));
    }

    public function test_user_can_grant_ability_on_specific_instance()
    {
        $user = $this->createUser();
        $post = $this->createPost();

        $user->grantAbility('edit', $post);

        $this->assertTrue($user->hasAbility('edit', $post));
    }

    public function test_user_can_revoke_ability()
    {
        $user = $this->createUser();
        $user->grantAbility('create', Post::class);

        $this->assertTrue($user->hasAbility('create', Post::class));

        $user->revokeAbility('create', Post::class);

        $this->assertFalse($user->hasAbility('create', Post::class));
    }

    public function test_user_can_grant_ability_with_context()
    {
        $user = $this->createUser();
        $workspace = $this->createWorkspace();

        $user->grantAbility('manage', Post::class, Workspace::class, $workspace->id);

        $this->assertTrue(
            $user->hasAbility('manage', Post::class, Workspace::class, $workspace->id)
        );
        $this->assertFalse($user->hasAbility('manage', Post::class));
    }

    public function test_user_can_grant_ability_with_conditions()
    {
        $user = $this->createUser();

        $user->grantAbility('edit', Post::class, null, null, ['status' => 'published']);

        $ability = Ability::where('assignable_id', $user->id)
            ->where('action', 'edit')
            ->first();

        $this->assertEquals(['status' => 'published'], $ability->conditions);
    }

    public function test_user_can_grant_permission()
    {
        $user = $this->createUser();

        $user->grantPermission('posts.create');

        $this->assertDatabaseHas('abilities', [
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
            'action' => 'permission:posts.create',
            'allowed' => true,
        ]);
    }

    public function test_user_can_check_permission()
    {
        $user = $this->createUser();
        $user->grantPermission('posts.create');

        $this->assertTrue($user->hasPermission('posts.create'));
        $this->assertFalse($user->hasPermission('posts.delete'));
    }

    public function test_user_can_revoke_permission()
    {
        $user = $this->createUser();
        $user->grantPermission('posts.create');

        $this->assertTrue($user->hasPermission('posts.create'));

        $user->revokePermission('posts.create');

        $this->assertFalse($user->hasPermission('posts.create'));
    }

    public function test_user_can_grant_permission_with_context()
    {
        $user = $this->createUser();
        $workspace = $this->createWorkspace();

        $user->grantPermission('posts.manage', Workspace::class, $workspace->id);

        $this->assertTrue(
            $user->hasPermission('posts.manage', Workspace::class, $workspace->id)
        );
        $this->assertFalse($user->hasPermission('posts.manage'));
    }

    public function test_abilities_relationship_returns_user_abilities()
    {
        $user = $this->createUser();
        $user->grantAbility('create', Post::class);
        $user->grantAbility('edit', Post::class);

        $abilities = $user->abilities;

        $this->assertCount(2, $abilities);
    }

    public function test_multiple_users_can_have_same_ability()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $user1->grantAbility('create', Post::class);
        $user2->grantAbility('create', Post::class);

        $this->assertTrue($user1->hasAbility('create', Post::class));
        $this->assertTrue($user2->hasAbility('create', Post::class));
    }

    public function test_revoking_ability_does_not_affect_other_users()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $user1->grantAbility('create', Post::class);
        $user2->grantAbility('create', Post::class);

        $user1->revokeAbility('create', Post::class);

        $this->assertFalse($user1->hasAbility('create', Post::class));
        $this->assertTrue($user2->hasAbility('create', Post::class));
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Whilesmart\Roles\RolesServiceProvider::class,
            \Cviebrock\EloquentSluggable\ServiceProvider::class,
        ];
    }
}
