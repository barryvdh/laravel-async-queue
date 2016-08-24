<?php
/**
 * Created by PhpStorm.
 * User: Renderer
 * Date: 11/28/2014
 * Time: 12:45 PM
 */

namespace Armxy\Queue\Models;


class Queue extends \Eloquent{

    protected $table = 'internal_queue';

    /**
     * Get first queued job from database.
     *
     *
     * @return \Armxy\Queue\Models\Job
     */
    function getFirstQueue(){

        $firstQueue = Queue::where('status', '=', Job::STATUS_OPEN)->orderBy('created_at')->first();

        if(!is_null($firstQueue)){

            $job = Job::find($firstQueue->id);

            return $job;
        }
    }
} 