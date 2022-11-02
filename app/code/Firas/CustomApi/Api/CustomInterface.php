<?php
namespace Firas\CustomApi\Api;

interface CustomInterface
{
/**
* GET for Post api
* @param string $shippingCountryCode two digit country code
* @param string $shippingCountryName
* @param float $shippingPrice
* @param string $shippingCurrency
* @return string shipping array collection.

*/

 public function getPost($shippingCountryCode, $shippingCountryName, $shippingPrice, $shippingCurrency);

 /**
* GET for Update api
* @param int $shipId
* @param float $updatedShippingPrice
* @return string

*/

 public function updatePost($shipId, $updatedShippingPrice);

  /**
* GET for Delete api
* @param int $shipId
* @return string

*/

 public function deletePost($shipId);

  /**
  * Returns cronjobs data
  *
  * @api
  * @param  string $cronstatus Cron status.
  * @param  string $createdat Cron status.
  * @return string cron array collection.
  */
 public function getCronList($cronstatus, $createdat);

}
