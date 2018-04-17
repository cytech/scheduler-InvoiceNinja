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

    public static $coreevents = [
        'quote' => 1,
        'invoice' => 2,
        'payment' => 4,
        'expense' => 8,
        'project' => 16,
        'task' => 32,
    ];

    public function isCoreeventEnabled($entityType)
    {
        if (! in_array($entityType, [
            'quote',
            'invoice',
            'payment',
            'expense',
            'project',
            'task',
        ])) {
            return true;
        }

        $enabledcoreevents = $this->where('setting_key', 'enabledCoreEvents')->value('setting_value');

        // note: single & checks bitmask match
        return $enabledcoreevents & static::$coreevents[$entityType];
    }

    public function coreeventsEnabled()
    {

        $filter = [];

        $enabledcoreevents = $this->where('setting_key', 'enabledCoreEvents')->value('setting_value');

        if ($enabledcoreevents == 0){
            $filter[] = 'none';
            return $filter;
        }

        foreach (static::$coreevents as $key => $value){
            if ($enabledcoreevents & $value){
                $filter[] = $key;
            }
        }

        return $filter;
    }

    public  function scopeLike($query, $field, $value){
        return $query->where($field, 'LIKE', "%$value%");
    }


}