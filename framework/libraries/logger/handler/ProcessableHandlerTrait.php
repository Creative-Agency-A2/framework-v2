<?php

namespace framework\libraries\logger\handler;

use framework\libraries\logger\interfaces\ResettableInterface;

trait ProcessableHandlerTrait
{
    /**
     * @var callable[]
     */
    protected $processors = [];

    /**
     * {@inheritdoc}
     */
    public function pushProcessor(callable $callback): \framework\libraries\logger\interfaces\LoggerHandlerInterface
    {
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function popProcessor(): callable
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * Processes a record.
     */
    protected function processRecord(array $record): array
    {
        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }

        return $record;
    }

    protected function resetProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }
}
