<?php

namespace ScandiWeb\Queries\Contract;
interface PRQ
{
public function InsertQuery($object,$connection);
public function SelectQuery($object,$connection);
public function DeleteQuery($object, $connection);
}