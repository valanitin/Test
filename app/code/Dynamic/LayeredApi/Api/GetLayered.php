<?php

namespace Dynamic\LayeredApi\Api;

interface GetLayered {

	/**
     * Returns Layered data
     *
     * @param int $categoryId
     * @return array
     */
    public function getLayeredList($categoryId);
}
