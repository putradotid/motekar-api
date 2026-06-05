<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(
        int $userId,
        string $action,
        string $module,
        string $description,
        array $data = []
    ): void {
        ActivityLog::create([
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'data'        => $data,
        ]);
    }
}