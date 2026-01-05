<?php

use App\Models\ActivityLog;

function log_activity($action, $description = null, $model = null, $modelId = null, $data = null)
{
    ActivityLog::create([
          'company_id'  => auth()->user()?->company_id, 
        'user_id'    => auth()->id(),
        'action'     => $action,
        'model'      => $model,
        'model_id'   => $modelId,
        'url'        => request()->fullUrl(),
        'ip_address' => request()->ip(),
        'description'=> $description,
        'data'       => $data,
    ]);
}
