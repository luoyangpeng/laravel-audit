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

namespace Itas\LaravelAudit\Listeners;

use Itas\LaravelAudit\Events\CreateRecorded;
use Itas\LaravelAudit\Mail\AuditTask;
use Itas\LaravelAudit\Models\Audit;
use Illuminate\Support\Facades\Mail;

class CreateAuditRecord
{
	 /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  CreateRecorded  $event
     * @return void
     */
    public function handle(CreateRecorded $event)
    {
        $object = $event->object;

        $auditUserId = current($object->users)['user_id'];

        $audited = Audit::create([
        	'auditable_id' => $object->model->id,
        	'auditable_type' => get_class($object->model),
            'audituser_id' => $auditUserId
        ]);


        $auditUsers = [];

        foreach ($object->users as $user) {
        	$auditUsers[] = ['user_id' => $user['user_id'], 'node' => $user['node'], 'sort' => $user['sort']];
        }

        $audited->auditUsers()->createMany($auditUsers);

        // 邮件通知
        $user = config('auth.providers.users.model')::where('id', $auditUserId)->first();
        Mail::to($user)->send(new AuditTask());
    }
}