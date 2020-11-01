<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string $dbId The database in which we will be connected
     */
    protected $dbId;

    public $queueLogId;


    protected function checkKeys($args, $requiredKeys){
        $missingKeys = [];
        $argKeys = array_keys($args);
        foreach($requiredKeys as $requiredKey){
            if(!in_array($requiredKey, $argKeys)){
                $missingKeys[]= $requiredKey;
            }
        }
        if(count($missingKeys)>0){
            throw new \Exception('keys: \"' . implode(', ', $missingKeys) . '\" not found in args for construction of ' . get_class($this));

        }
    }
}
