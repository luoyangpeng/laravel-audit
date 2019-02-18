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

namespace Itas\LaravelAudit\Traits;


Trait HasAudit
{
    public $users;

	/**
     * Return audit record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphOne
     */
	public function audit()
	{
		return $this->morphOne(config('audit.audit_model'), 'auditable');
	}

    /**
     * Set Audit User
     *
     * @param $users
     */
    public function setAuditUser($users)
    {
        $this->users = $users;
    }

    /**
     * Get Audit User
     *
     * @return mixed
     */
    public function getAuditUser()
    {
        return $this->users;
    }

    /**
     * User node audit permission
     *
     * @param $userId
     * @return bool
     */
    public function hasNodeAuditPermission($userId)
    {
        $auditUsers = $this->getAuditUser();

        if (!$auditUsers) {
            return true;
        }

        if (in_array($auditUsers, $userId)) {
            return true;
        }

        return false;
    }
}