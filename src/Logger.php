<?php

namespace SoulDoit\ActivityLogger;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Http\Events\RequestHandled;
use SoulDoit\ActivityLogger\Exceptions\SingleLogModeNotAllowedToCallThisMethod;
use SoulDoit\ActivityLogger\Exceptions\TrackedLoggingAlreadyStopped;
use Log, Event;

class Logger
{
    private $log_instance;
    private $is_single_log = true;
    private $log_text_prepend = '';
    private $log_label;
    private $code;
    private $ref;
    private $start_time;
    private $is_stop;

    function __construct(string $channel_name)
    {
        $this->log_instance = Log::channel($channel_name);
    }

    public function setTrack(string $code, bool $auto_stop=true, string $log_label="LOG")
    {
        $this->is_stop = false;
        $this->is_single_log = false;
        $this->code = $code;
        $this->ref = rand(100000,999999);
        $this->log_text_prepend = "[$this->code][REF: $this->ref]";
        $this->log_label = $log_label;
        $this->start_time = microtime(true);

        $this->log_instance->info("$this->log_text_prepend START $this->log_label");
        
        if ($auto_stop) {
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
        if ($this->is_single_log) {
            $this->log_instance->info($activity);
        } else {
            $this->log_instance->info("$this->log_text_prepend -- $activity");
        }
    }

    public function stop()
    {
        $is_throw_error = config('sd-activity-logger.throw_error_if_failed_stop', true);

        if ($this->is_single_log) {
            if ($is_throw_error) throw SingleLogModeNotAllowedToCallThisMethod::create('stop');
            else return false;
        }
        if ($this->is_stop) {
            if ($is_throw_error) throw TrackedLoggingAlreadyStopped::create();
            else return false;
        }

        $total_secs = number_format(microtime(true)-($this->start_time), 4);
        $this->log_instance->info("$this->log_text_prepend STOP $this->log_label (Total Time: $total_secs secs)");
        $this->is_stop = true;
        $this->is_single_log = true;
    }
}
