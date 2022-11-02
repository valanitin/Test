<?php

namespace Dynamic\Cmspagemanager\Api;

interface GetCmspagemanager {

	/**
     * Returns Homepage data
     *
     * @param int $pageId
     * @return array
     */
    public function getCmspagemanagerList($pageId);
}
