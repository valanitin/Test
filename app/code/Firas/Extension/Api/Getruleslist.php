<?php
namespace Firas\Extension\Api;
interface Getruleslist {

	/**
	     * Returns orders data to user
	     *
	     * @api
			 * @param  string $phoneNumber customer phone number.
	     * @param  string $websiteCode Website Code.
	     * @return return order array collection.
	     */
	    public function getList();
}
