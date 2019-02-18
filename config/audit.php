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


return [
    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',
    /*
     * Table name for audit.
     */
    'audit_table' => 'audit',
     /*
     * Table name for audit users.
     */
    'audit_user_table' => 'audit_user',
     /*
     * Table name for audit records.
     */
    'audit_record_table' => 'audit_record',
    /*
     * Model name for audit record.
     */
    'audit_model' => 'Itas\LaravelAudit\Models\Audit',
];
