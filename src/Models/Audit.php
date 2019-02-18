<?php

/**
 * This file is part of itas/laravel-audit.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    itas<luoylangpeng@gmail.com>
 * @copyright itas<luoylangpeng@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */ 

namespace Itas\LaravelAudit\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Audit extends Model
{
    protected $guarded = [];

    const WAIT_AUDIT = 0;
    const SUCCESS_AUDIT = 1;
    const FAIL_AUDIT = 2;

    /**
     * Audit constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = \config('audit.audit_table');
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function auditUsers()
    {
        return $this->hasMany(AuditUser::class);
    }

    public function auditRecords()
    {
        return $this->hasMany(AuditRecord::class);
    }

    /**
     * Get current audit user.
     *
     * @return mixed
     */
    public function currentAuditUser()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'audituser_id');
    }
}