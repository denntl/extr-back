<?php

namespace App\Services\Common\DataListing\DTO;

class JoinDTO
{
    public string $tableName;
    public string $condition;
    public string $type;

    public function __construct(string $tableName, string $condition, string $type)
    {
        $this->tableName = $tableName;
        $this->condition = $condition;
        $this->type = $type;
    }
}
