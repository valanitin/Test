<?php
namespace Diligent\Geoip\Helper;

class Address extends \Magentizer\GeoIP\Helper\Address
{
	public $ipAddress;
	
	
	/**
     * @param null $storeId
     *
     * @return array
     */
     
     
     
     
    public function getGeoIpData($storeId = null)
    {

        try {
            $libPath = $this->checkHasLibrary();
            if ($this->isEnabled($storeId) && $libPath && class_exists('GeoIp2\Database\Reader')) {
                $geoIp  = new Reader($libPath, $this->getLocales());
                $record = $geoIp->city($this->getIpAddress());

                $geoIpData = [
                    'city'       => $record->city->name,
                    'country_id' => $record->country->isoCode,
                    'postcode'   => $record->postal->code
                ];

                if ($record->mostSpecificSubdivision) {
                    $code = $record->mostSpecificSubdivision->isoCode;
                    if ($regionId = $this->_regionModel->loadByCode($code, $record->country->isoCode)->getId()) {
                        $geoIpData['region_id'] = $regionId;
                    } else {
                        $geoIpData['region'] = $record->mostSpecificSubdivision->name;
                    }
                }
            } else {
                $geoIpData = [];
            }
        } catch (Exception $e) {
            // No Ip found in database
            $geoIpData = [];
        }
		
		$this->ipAddress = $this->getIpAddress();
		$contents = $this->getApiData($this->getIpApiUrl());
        $geoData = json_decode($contents);
            
		if(empty($geoData) || $geoData->status == 'fail'){
			$contents = $this->getApiData($this->getIpInfoUrl());
			$geoData = json_decode($contents);
		}
		
		if(isset($geoData->city)){
			$geoIpData['city']  = $geoData->city;
		}
		if(isset($geoData->postal)){
			$geoIpData['postcode']  = $geoData->postal;
		}else if(isset($geoData->zip)){
			$geoIpData['postcode']  = $geoData->zip;
		}
		if(isset($geoData->countryCode)){
			$geoIpData['country_id']  = $geoData->countryCode;
		}else if(isset($geoData->country)){
			$geoIpData['country_id']  = $geoData->country;
		}
		
        return $geoIpData;
    }
	
	public function getApiData($url)
    {
        if(empty($url)) {
            $url = $this->getIpApiUrl();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        //Tell cURL that it should only spend 5 seconds to connect to the URL.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
         
        //A given cURL operation should only take 5 seconds max.
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $contents = curl_exec($ch);
        curl_close($ch);  
        return $contents;
    }
    
    public function getIpApiUrl()
    {
       return "http://ip-api.com/json/".$this->ipAddress; 
    }
    
    public function getIpInfoUrl()
    {
        //get env variable
        $geoip_token = '6bbcd474d5141c';
        $url = "http://ipinfo.io/".$this->ipAddress."/geo?token=".$geoip_token;
        
        return $url;
    }
	
}