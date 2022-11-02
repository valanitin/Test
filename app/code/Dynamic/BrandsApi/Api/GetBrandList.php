<?php

namespace Dynamic\BrandsApi\Api;

interface GetBrandList {

	/**
     * Returns brand data
     *
     * @api
     * @return return brand array collection.
     */
    public function getList();
}
