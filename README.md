# Laravel-audit

## Installation

The laravel-audit Service Provider can be installed via [Composer](http://getcomposer.org) by requiring the
`itas/laravel-audit` package and setting the `minimum-stability` to `dev` (required for Laravel 5) in your
project's `composer.json`.

```json
{
    "require": {
        "itas/laravel-audit": "~1.0"
    }
}
```

or

Require this package with composer:
```
composer require itas/laravel-audit 
```

Update your packages with ```composer update``` or install with ```composer install```.


## Usage

To use the laravel-audit Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `config/app.php` and register the laravel-audit Service Provider.

```php
    'providers' => [
        // ...
        itas\LaravelAudit\AuditServiceProvider::class,
    ]
```

## Configuration

To use your own settings, publish config.

```$ php artisan vendor:publish```

`config/audit.php`


## Last Step
run:
```$ php artisan migrate```


## Demo
```php
<?php

namespace App\Model;

use Itas\LaravelAudit\Traits\HasAudit;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasAudit;

    protected $table = 'leave';
}

```

```php
<?php

namespace App\Http\Controllers;

use Itas\LaravelAudit\Events\CreateRecorded;
use App\Model\Leave;

class LeaveController extends Controller
{
    // 创建请假单时，生成请假审核流
    public function create(Leave $leave)
    {
        $leave = $leave->create([]);

        $object = collect();
        $object->model = $leave;
        $object->users = [
            [
                'user_id' => 1,
                'node' => '组长',
                'sort' => 1
            ],
            [
                'user_id' => 2,
                'node' => '副总监',
                'sort' => 2
            ],
            [
                'user_id' => 3,
                'node' => '技术总监',
                'sort' => 3
            ],
            [
                'user_id' => 4,
                'node' => '人事',
                'sort' => 4
            ],
        ];
        event(new CreateRecorded($object));
    }
    
    // 显示审核流程图
    public function index(Leave $leave)
    {
        $audit = $leave->with('audit', 
                            'audit.currentAuditUser', 
                            'audit.auditUsers', 
                            'audit.auditRecords', 
                            'audit.auditUsers.auditer')
                        ->find(1)
                        ->toArray();
        
        return view('audit.stream', compact('audit'));
    }
}

```

## 效果图
![图片](https://github.com/luoyangpeng/laravel-audit/raw/master/images/audit1.png)

![图片](https://github.com/luoyangpeng/laravel-audit/raw/master/images/audit2.png)

![图片](https://github.com/luoyangpeng/laravel-audit/raw/master/images/audit3.png)
