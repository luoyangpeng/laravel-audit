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

namespace Itas\LaravelAudit\Controllers;

use Illuminate\Routing\Controller;
use Itas\LaravelAudit\Models\AuditRecord;
use Itas\LaravelAudit\Models\AuditUser;
use Itas\LaravelAudit\Models\Audit;
use Illuminate\Support\Facades\Mail;
use Itas\LaravelAudit\Mail\AuditTask;

class AuditController extends Controller
{
    public function audit()
    {
        $userId = request('user_id');
        $auditId = request('audit_id');
        $remark = request('remark');
        $status = request('status');

        if (empty($userId) || empty($auditId)) {
            return response()->json([
                'code' => 400,
                'message' => '审核用户id和审核记录id不能为空！'
            ]);
        }

        if ($status == Audit::FAIL_AUDIT && empty($remark)) {
            return response()->json([
                'code' => 400,
                'message' => '备注不能为空！'
            ]);
        }

        $audit = Audit::find($auditId);

        if (empty($audit)) {
            return response()->json([
                'code' => 400,
                'message' => '没有找到数据！'
            ]);
        }

        if ($audit->status == 1) {
            return response()->json([
                'code' => 400,
                'message' => '记录已经审核通过了！'
            ]);
        }

        if ($audit->audituser_id != 0 && $audit->audituser_id != auth()->id()) {
            return response()->json([
                'code' => 400,
                'message' => '你没有审核权限！'
            ]);
        }

        $auditRecord = AuditRecord::where('user_id', $userId)->where('audit_id', $auditId)->exists();

        if ($auditRecord) {
            return response()->json([
                'code' => 400,
                'message' => '您已经审核过了！'
            ]);
        }

        \DB::beginTransaction();

        try {
            // 添加审核记录
            AuditRecord::create([
                'user_id' => $userId,
                'audit_id' => $auditId,
                'remark' => $remark,
                'status' => $status
            ]);

            // 更新用户审核状态
            AuditUser::where('user_id', $userId)->where('audit_id', $auditId)->update(['status' => $status]);

            $currentAuditUser = AuditUser::where('user_id', $userId)->where('audit_id', $auditId)->first();

            $nextAuditUser = [];

            if ($currentAuditUser) {
                $nextAuditUser = AuditUser::where('audit_id', $auditId)->where('sort', $currentAuditUser->sort + 1)->first();
            }

            if ($status == Audit::FAIL_AUDIT || empty($nextAuditUser)) {
                $auditUserId = 0;
            } else {
                $auditUserId = $nextAuditUser->user_id;
            }

            // 审核完成
            if (empty($nextAuditUser) && $status == Audit::SUCCESS_AUDIT) {
                Audit::where('id', $auditId)->update(['audituser_id' => $auditUserId, 'status' => Audit::SUCCESS_AUDIT]);
            }

            // 审核失败
            if ($status == Audit::FAIL_AUDIT) {
                Audit::where('id', $auditId)->update(['audituser_id' => $auditUserId, 'status' => Audit::FAIL_AUDIT]);
            }

            if ($nextAuditUser && $status == Audit::SUCCESS_AUDIT) {
                Audit::where('id', $auditId)->update(['audituser_id' => $auditUserId]);

                // 邮件通知
                $user = config('auth.providers.users.model')::where('id', $auditUserId)->first();
                Mail::to($user)->send(new AuditTask());
            }
        } catch(\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'code' => 400,
                'message' => $e->getMessage()
            ]);
        }

        \DB::commit();

        
        return response()->json([
            'code' => 200,
            'message' => 'success！'
        ]);
    }
}