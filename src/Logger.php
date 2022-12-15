<?php

namespace SoulDoit\ActivityLogger;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Log, Event;

use SoulDoit\ActivityLogger\Exceptions\SingleLogModeNotAllowedToCallThisMethod;
use SoulDoit\ActivityLogger\Exceptions\TrackedLoggingAlreadyStopped;

class Logger
{
    private $log_instance;
    private $is_single_log = true;
    private $log_text_prepend = '';
    private $log_label;
    private $code;
    private $ref;
    private $start_time;
    private $is_stop = false;

    function __construct(string $channel_name)
    {
        $this->log_instance = Log::channel($channel_name);
        $this->start_time = microtime(true);
    }

    public function setTrack(string $code, bool $auto_stop=true, string $log_label="LOG")
    {
        $this->is_single_log = false;
        $this->code = $code;
        $this->ref = rand(100000,999999);
        $this->log_text_prepend = "[$this->code][REF: $this->ref]";
        $this->log_label = $log_label;

        $this->log_instance->info("$this->log_text_prepend START $this->log_label");
        
        if($auto_stop){
            Event::listen(function (CommandFinished $event) {
                $this->stop();
            });
            Event::listen(function (RequestHandled $event) {
                $this->stop();
            });
        }
    }

    public function log(string $activity)
    {
        if($this->is_single_log){
            $this->log_instance->info($activity);
        }else{
            $this->log_instance->info("$this->log_text_prepend -- $activity");
        }
    }

    public function stop()
    {
        if($this->is_single_log) throw SingleLogModeNotAllowedToCallThisMethod::create('stop');
        if($this->is_stop) throw TrackedLoggingAlreadyStopped::create();

        $total_secs = number_format(microtime(true)-($this->start_time), 4);
        $this->log_instance->info("$this->log_text_prepend STOP $this->log_label (Total Time: $total_secs secs)");
        $this->is_stop = true;
        $this->is_single_log = true;
    }
}
