<?php

namespace App\Observers;

use App\Support\HomepageCache;
use Illuminate\Database\Eloquent\Model;

class HomepageCacheObserver
{
    public function saved(Model $model): void
    {
        $this->invalidate();
    }

    public function deleted(Model $model): void
    {
        $this->invalidate();
    }

    public function restored(Model $model): void
    {
        $this->invalidate();
    }

    public function forceDeleted(Model $model): void
    {
        $this->invalidate();
    }

    private function invalidate(): void
    {
        HomepageCache::forget();
    }
}
