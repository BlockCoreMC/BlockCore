<?php
declare(strict_types=1);

namespace blockcore\logger;

use pmmp\thread\ThreadSafeArray;
use pmmp\thread\Runnable;
use Stringable;

class BlockLogger extends Runnable implements \Logger {
    private string $name;
    private bool $debugMode;
    private ThreadSafeArray $logLevels;
    private string $format = "[%s] [%s/%s]: %s";

    public function __construct(string $name = "RakLib", bool $debugMode = false) {
        $this->name = $name;
        $this->debugMode = $debugMode;
        $this->logLevels = new ThreadSafeArray();
        $this->logLevels->merge([
            'emergency', 'alert', 'critical', 'error',
            'warning', 'notice', 'info', 'debug'
        ]);
    }

    protected function send($message, string $level, string $prefix): void {
        $time = date("H:i:s");
        $formatted = sprintf($this->format, $time, $this->name, $prefix, $message);
        
        $this->synchronized(function() use ($formatted) {
            echo $formatted . PHP_EOL;
        });
    }

    public function emergency($message) { $this->send($message, \LogLevel::EMERGENCY, "EMERGENCY"); }
    public function alert($message)     { $this->send($message, \LogLevel::ALERT, "ALERT"); }
    public function critical($message)  { $this->send($message, \LogLevel::CRITICAL, "CRITICAL"); }
    public function error($message)     { $this->send($message, \LogLevel::ERROR, "ERROR"); }
    public function warning($message)   { $this->send($message, \LogLevel::WARNING, "WARNING"); }
    public function notice($message)    { $this->send($message, \LogLevel::NOTICE, "NOTICE"); }
    public function info($message)      { $this->send($message, \LogLevel::INFO, "INFO"); }
    
    public function debug($message) {
        if($this->debugMode) {
            $this->send($message, \LogLevel::DEBUG, "DEBUG");
        }
    }

    public function log($level, $message) {
        if(!isset($this->logLevels[$level])) {
            $level = 'info';
        }
        $this->{$level}($message);
    }

    public function logException(\Throwable $e, $trace = null) {
        $this->critical("Exception: " . $e->getMessage());
        $this->critical("File: " . $e->getFile() . ":" . $e->getLine());
        
        if($trace === null) {
            $trace = $e->getTrace();
        }
        
        foreach($trace as $i => $line) {
            $this->critical(sprintf(
                "#%d %s(%d): %s%s%s()",
                $i,
                $line["file"] ?? "unknown",
                $line["line"] ?? 0,
                $line["class"] ?? "",
                $line["type"] ?? "",
                $line["function"] ?? ""
            ));
        }
    }

    public function run(): void {
        // Implementación para ejecución en hilo separado
    }
}
