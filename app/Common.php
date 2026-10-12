<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('record_audit_log')) {
    /**
     * Record an audit event into audit_logs table
     */
    function record_audit_log(
        string $action,
        string $objectType,
        int $objectId,
        ?string $reason = null,
        ?array $previousState = null,
        ?array $newState = null,
        ?int $actorId = null,
        ?int $regionId = null
    ) {
        $auditModel = new \App\Models\AuditLogModel();
        return $auditModel->record(
            $actorId,
            $action,
            $objectType,
            $objectId,
            $previousState,
            $newState,
            $reason,
            $regionId
        );
    }
}
