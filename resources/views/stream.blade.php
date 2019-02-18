<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <title>审核流程图</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }
        *, ::after, ::before {
            box-sizing: content-box;
        }
        ul li {
            list-style: none;
        }

        .package-status {
            padding: 18px 0 0 0
        }

        .package-status .status-box {
            overflow: hidden
        }
        .package-status .status-list {
            margin-left: 8px;
            margin-top: -5px;
            padding-left: 8px;
        }
        .package-status .status-list>li {
            height: auto;
            width: 95%;
        }
        .package-status .status-box {
            position: relative
        }
        .package-status .status-box:before {
            content: " ";
            background-color: #f3f3f3;
            display: block;
            position: absolute;
            top: -8px;
            left: 20px;
            width: 10px;
            height: 4px
        }

        .package-status .status-list>li {
            border-left: 2px solid #eaebed;
            text-align: left;
        }
        .package-status .status-list>li.success {
            border-color: #94b177;
            color: #94b177;
        }
        .package-status .status-list>li.fail {
            border-color: #ff0000;
            color: #ff0000;
        }
        .package-status .status-list>li.active {
            border-color: #0278D8;
            color: #0278D8;
        }

        .package-status .status-list>li:before {
            /* 流程点的样式 */
            content: '';
            border: 3px solid #eaebed;
            background-color: #ffffff;
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 10px;
            margin-left: -7px;
            margin-right: 10px
        }
        .package-status .status-list>li.success:before {
            border-color: #94b177;
        }
        .package-status .status-list>li.fail:before {
            border-color: #ff0000;
        }
        .package-status .status-list>li.active:before {
            border-color: #0278D8;
        }
        .package-status .status-list {
            margin: 0 0 0 20px;
        }
        .status-list>li:not(:first-child) {
            padding-top: 10px;
        }
        .status-content-before {
            text-align: left;
            margin-left: 25px;
            margin-top: -20px;
        }

        .status-time-before {
            text-align: left;
            margin-left: 25px;
            font-size: 10px;
            margin-top: 5px;
        }
        .status-remark-before {
            text-align: left;
            margin-left: 25px;
            font-size: 10px;
            margin-top: 5px;
        }
        .status-line {
            border-bottom: 1px solid #ccc;
            margin-left: 25px;
            margin-top: 10px;
        }
        .audit-status {
            margin: 0 0 10px 28px;
            border-left: 3px solid #0278D8;
            padding-left: 22px;
            height: 20px;
        }
        .wait-audit {
            color: #000000;
        }
        .success-audit {
            color: #94b177;
        }
        .fail-audit {
            color: #ff0000;
        }
        .loading-audit {
            color: #0278D8;
        }
        .audit-button {
            height: 30px;
            background: #ccc;
            text-align: center;
            line-height: 30px;
            border-radius: 10%;
            float: right;
            position: relative;
            top: -34px;
        }
    </style>
</head>
<body>
<div class="package-status">
    <div class="status-box">
        <div class="audit-status">审核状态：
            @if($audit['audit']['status'] == 0)
                <span class="wait-audit">待审核</span>
            @elseif($audit['audit']['status'] == 1)
                <span class="success-audit">审核通过</span>
            @elseif($audit['audit']['status'] == 2)
                <span class="fail-audit">审核不通过</span>
            @endif
        </div>
        <div style="padding: 0 20px 0 20px;">
            <p style="border-bottom: 1px solid #cccccc;margin: 0 0 10px 0;"></p>
        </div>

        <ul class="status-list">
            @foreach($audit['audit']['audit_users'] as $user)
                @if($user['status'] == 0 && $user['auditer']['id'] == $audit['audit']['current_audit_user']['id'])
                    <li class="active">
                        <div class="status-content-before">等待{{$user['node']}}审核（{{$user['auditer']['name']}}）</div>
                        @if($audit['audit']['current_audit_user']['id'] == auth()->id() || 1)
                        <div class="audit-button">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#auditModal">
                                审核
                            </button>
                        </div>
                        @endif
                        <div class="status-line"></div>
                    </li>
                @elseif($user['status'] == 1)
                <li class="success">
                    <div class="status-content-before">{{$user['node']}}审核通过（{{$user['auditer']['name']}}）</div>
                    <div class="status-time-before">{{$user['updated_at']}}</div>
                    <div class="status-line"></div>
                </li>
                @elseif($user['status'] == 2)
                    <li class="fail">
                        <div class="status-content-before">{{$user['node']}}审核不通过（{{$user['auditer']['name']}}）</div>
                        <div class="status-time-before">{{$user['updated_at']}}</div>
                        @foreach($audit['audit']['audit_records'] as $record)
                            @if($record['user_id'] == $user['user_id'])
                                <div class="status-remark-before">备注: {{$record['remark']}}</div>
                            @endif
                        @endforeach
                        <div class="status-line"></div>
                    </li>
                @else
                    <li>
                        <div class="status-content-before">{{$user['node']}}审核（{{$user['auditer']['name']}}）</div>
                        <div class="status-line"></div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">审核</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">备注</span>
                        </div>
                        <input type="hidden" name="audit_id" value="{{$audit['audit']['id']}}" id="auditId">
                        <input type="hidden" name="user_id" value="{{auth()->id()}}" id="userId">
                        <textarea class="form-control" aria-label="With textarea" name="remark" id="remark"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger">审核不通过</button>
                    <button type="button" class="btn btn-success">审核通过</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".btn-danger").on('click', function () {
        var remark = $("#remark").val();
        var auditId = $("#auditId").val();
        var userId = $("#userId").val();

        if (!remark) {
            alert('请填写不通过原因');
            return false;
        }

        var data = {audit_id:auditId, user_id:userId, remark:remark, status:2};

        $.ajax({
            url:'/itas/audit',
            type:'post',
            data:data,
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                $('#auditModal').modal('hide');

                if (data.code != 200) {
                    toastr.error(data.message);
                } else {
                    toastr.success('操作成功！');
                }

                setTimeout(function () {
                    location.reload();
                }, 1000);
            },
            error:function () {

            }
        });

    });
    $(".btn-success").on('click', function () {
        var remark = $("#remark").val();
        var auditId = $("#auditId").val();
        var userId = $("#userId").val();

        var data = {audit_id:auditId, user_id:userId, remark:remark, status:1};

        $.ajax({
            url:'/itas/audit',
            type:'post',
            data:data,
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                $('#auditModal').modal('hide');

                if (data.code != 200) {
                    toastr.error(data.message);
                } else {
                    toastr.success('操作成功！');
                }

                setTimeout(function () {
                    location.reload();
                }, 1000);
            },
            error:function () {

            }
        });
    });
</script>
</body>

</html>

