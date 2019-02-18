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

namespace Itas\LaravelAudit\Events;

use Illuminate\Queue\SerializesModels;

class CreateRecorded
{
	use SerializesModels;

	public $object;


	public function __construct($object)
	{
		$this->object = $object;
	}

}