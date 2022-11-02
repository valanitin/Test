<?php

namespace Dynamic\SizeApi\Api;

interface Getsize {

	/**
     * Returns size data
     *
     * @param int $categoryId
     * @return array
     */
    public function getSizeList($categoryId);
}
