<?php
namespace ScandiWeb\DataBase\Contract;
interface  IDBConnection
{
public function connect();
public function close();
}