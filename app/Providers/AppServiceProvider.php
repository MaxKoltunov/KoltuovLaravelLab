<?php

namespace App\Providers;

use App\Models\Turtle;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('turtles.create', fn (User $user): bool => true);

        // Любой авторизованный пользователь может комментировать активную запись.
        Gate::define('comments.create', fn (User $user, Turtle $turtle): bool =>
            ! $turtle->trashed());

        Gate::define('turtles.update', fn (User $user, Turtle $turtle): bool =>
            ! $turtle->trashed() && ($user->isAdmin() || $turtle->user_id === $user->id));

        Gate::define('turtles.delete', fn (User $user, Turtle $turtle): bool =>
            ! $turtle->trashed() && ($user->isAdmin() || $turtle->user_id === $user->id));

        Gate::define('turtles.view-deleted', fn (User $user): bool => $user->isAdmin());

        Gate::define('turtles.restore', fn (User $user, Turtle $turtle): bool =>
            $user->isAdmin() && $turtle->getRawOriginal('deleted_at') !== null);

        Gate::define('turtles.force-delete', fn (User $user, Turtle $turtle): bool =>
            $user->isAdmin() && $turtle->trashed());
    }
}
