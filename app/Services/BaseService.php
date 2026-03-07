<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseService
{
    /**
     * Execute a database transaction with logging and error handling.
     *
     * @param callable $callback
     * @param string $actionName For logging purposes
     * @return ServiceResponse
     */
    protected function handleTransaction(callable $callback, string $actionName): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();

            Log::info("Action '{$actionName}' completed successfully.");

            return ServiceResponse::success($result, "{$actionName} successful.");
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Action '{$actionName}' failed: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return ServiceResponse::error("Failed to perform '{$actionName}': " . $e->getMessage(), 500, $e);
        }
    }
}
