<?php

namespace framework\libraries\logger;

use DateTimeZone;

class DateTimeImmutable extends \DateTimeImmutable implements \JsonSerializable
{
    /**
     * @var bool
     */
    private $useMicroseconds;

    public function __construct(bool $useMicroseconds, ?DateTimeZone $timezone = null)
    {
        $this->useMicroseconds = $useMicroseconds;

        parent::__construct('now', $timezone);
    }

    public function jsonSerialize(): string
    {
        if ($this->useMicroseconds) {
            return $this->format('Y-m-d\TH:i:s.uP');
        }

        return $this->format('Y-m-d\TH:i:sP');
    }

    public function __toString(): string
    {
        return $this->jsonSerialize();
    }
}