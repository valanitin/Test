<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magentizer\Pricing\Api\Data\DataInterface;

interface DataRepositoryInterface
{

    /**
     * @param DataInterface $data
     * @return mixed
     */
    public function save(DataInterface $data);


    /**
     * @param $dataId
     * @return mixed
     */
    public function getById($dataId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magentizer\Pricing\Api\Data\DataSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param DataInterface $data
     * @return mixed
     */
    public function delete(DataInterface $data);

    /**
     * @param $dataId
     * @return mixed
     */
    public function deleteById($dataId);
}
