<?php
namespace Firas\ApiConfigValue\Api;

interface CustomInterface
{

/**
* GET for Update api
* @return array
*/

public function updateConfigValue();

/**
* GET for Get api
* @param mixed $data
* @return array
*/

public function getConfigValue($data);

}
