<?php
namespace framework\libraries\logger\handler;

use framework\libraries\logger\interfaces\LoggerFormatterInterface;
use framework\libraries\logger\formatter\lineFormatter;

trait FormatableHandlerTrait
{
    /**
     * @var ?FormatterInterface
     */
    protected $formatter;

    /**
     * {@inheritdoc}
     */
    public function setFormatter(LoggerFormatterInterface $formatter): \framework\libraries\logger\interfaces\LoggerHandlerInterface
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): LoggerFormatterInterface
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    /**
     * Gets the default formatter.
     *
     * Overwrite this if the LineFormatter is not a good default for your handler.
     */
    protected function getDefaultFormatter(): LoggerFormatterInterface
    {
        return new lineFormatter();
    }
}
