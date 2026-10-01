<?php

namespace App\Providers;

use App\Models\Grade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
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
        Paginator::defaultView('pagination.gradeflow');

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $notifications = collect();
            $latest = null;

            if ($user?->isAdmin()) {
                $grades = Grade::where('status', 'submitted');
                $count = $grades->count();
                if ($count) $notifications->push(['text' => "$count submitted grade(s) awaiting release", 'url' => route('grades.index', ['status' => 'submitted'])]);
                if ($count) $latest = $grades->max('updated_at');
            } elseif ($user?->isInstructor()) {
                $grades = Grade::where('status', 'draft')->whereHas('enrollment.classAssignment.instructor', fn ($query) => $query->where('user_id', $user->id));
                $count = $grades->count();
                if ($count) $notifications->push(['text' => "$count draft grade(s) still pending", 'url' => route('grades.index', ['status' => 'draft'])]);
                if ($count) $latest = $grades->max('updated_at');
            } elseif ($user?->isStudent()) {
                $grades = Grade::where('status', 'released')->whereHas('enrollment.student', fn ($query) => $query->where('user_id', $user->id));
                $count = $grades->count();
                if ($count) $notifications->push(['text' => "$count released grade(s) available", 'url' => route('grades.index')]);
                if ($count) $latest = $grades->max('updated_at');
            }

            $view->with('notifications', $notifications)
                ->with('showNotificationDot', $latest && (! $user->notifications_seen_at || Carbon::parse($latest)->greaterThan($user->notifications_seen_at)));
        });
    }
}
