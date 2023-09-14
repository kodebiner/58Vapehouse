<?php

namespace DatePeriod;

class DatePeriod implements IteratorAggregate {
    /* Constants */
    public const int EXCLUDE_START_DATE;
    public const int INCLUDE_END_DATE;
    /* Properties */
    public readonly ?DateTimeInterface $start;
    public readonly ?DateTimeInterface $current;
    public readonly ?DateTimeInterface $end;
    public readonly ?DateInterval $interval;
    public readonly int $recurrences;
    public readonly bool $include_start_date;
    public readonly bool $include_end_date;
    /* Methods */
    public __construct(
        DateTimeInterface $start,
        DateInterval $interval,
        int $recurrences,
        int $options = 0
    )
    public __construct(
        DateTimeInterface $start,
        DateInterval $interval,
        DateTimeInterface $end,
        int $options = 0
    )
    public __construct(string $isostr, int $options = 0)
    public getDateInterval(): DateInterval
    public getEndDate(): ?DateTimeInterface
    public getRecurrences(): ?int
    public getStartDate(): DateTimeInterface
}