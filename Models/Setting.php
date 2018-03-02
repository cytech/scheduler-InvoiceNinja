<?php

/**
 * *
 *  * This file is part of Schedule Addon for InvoiceNinja.
 *  * (c) Cytech <cytech@cytech-eng.com>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 *
 */



namespace Modules\Scheduler\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'schedule_settings';

    public  function scopeLike($query, $field, $value){
        return $query->where($field, 'LIKE', "%$value%");
    }


}