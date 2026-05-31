<?php

declare(strict_types=1);

namespace Core;

abstract class Seeder
{
    protected int $priority = 100;

    abstract public function run(): void;

    public function getPriority(): int
    {
        return $this->priority;
    }
}
