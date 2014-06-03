<?php namespace Barryvdh\Queue\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Translation model
 *
 * @property integer $id
 * @property integer $status
 * @property integer $retries
 * @property integer $seconds
 * @property string  $payload
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Queue extends Model{

    protected $table = 'laq_async_queue';
    protected $guarded = array('id', 'created_at', 'updated_at');

}