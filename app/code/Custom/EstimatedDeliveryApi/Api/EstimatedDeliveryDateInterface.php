<?php

namespace Custom\EstimatedDeliveryApi\Api;

interface EstimatedDeliveryDateInterface
{
  /**
  * GET for Post api
  * @param string $value
  * @return string
  */

	public function getEstimateDate($id);
}