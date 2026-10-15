<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Barang;
use Illuminate\Support\Facades\Request;

class BarangObserver
{
    private function log(Barang $b, string $action, ?array $old = null, ?array $new = null): void
    {
        if (!auth()->check() && app()->runningInConsole()) return;
        AuditLog::create([
            'user_id' => auth()->id(),
            'model_type' => Barang::class,
            'model_id' => $b->id,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'url' => Request::fullUrl(),
            'ip' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
        ]);
    }
    public function created(Barang $b): void { $this->log($b, 'created', null, $b->getAttributes()); }
    public function updated(Barang $b): void { $this->log($b, 'updated', $b->getOriginal(), $b->getAttributes()); }
    public function deleted(Barang $b): void { $this->log($b, 'deleted', $b->getOriginal(), null); }
}
