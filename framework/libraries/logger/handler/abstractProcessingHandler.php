<?php

namespace framework\libraries\logger\handler;

use framework\libraries\logger\interfaces\LoggerProcessableHandlerInterface;
use framework\libraries\logger\interfaces\LoggerFormatableHandlerInterface;

abstract class abstractProcessingHandler extends abstractHandler implements 
    LoggerProcessableHandlerInterface,
    LoggerFormatableHandlerInterface
{

    use ProcessableHandlerTrait;
    use FormatableHandlerTrait;

    /**
     * Еще одна реализация обработчика
     *
     * @param array $record
     * @return boolean
     */
    public function handle(array $record) : bool
    {
        if (!$this->isHandling($record)){
            return false;
        }
        if ($this->processors){
            $record = $this->processRecord($record);
        }

        $record['formatted'] = $this->getFormatter()->format($record);
        $this->write($record);

        return false === $this->bubble;
    }

    abstract protected function write(array $record) : void;

    public function reset()
    {
        parent::reset();
        $this->resetProcessors();
    }
}