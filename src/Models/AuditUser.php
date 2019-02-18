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

use Illuminate\Database\Eloquent\Model;


class AuditUser extends Model
{
    protected $guarded = [];

    /**
     * AuditUser constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = \config('audit.audit_user_table');
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Return auditer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function auditer()
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('audit.user_foreign_key'));
    }
}