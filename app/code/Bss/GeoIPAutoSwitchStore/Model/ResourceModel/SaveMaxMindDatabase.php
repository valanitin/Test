<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GeoIPAutoSwitchStore\Model\ResourceModel;

use Magento\Setup\Exception;

class SaveMaxMindDatabase
{

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpMaxMind
     */
    private $geoIpMaxMind;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvHandle;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig
     */
    private $geoIpConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * SaveMaxMindDatabase constructor.
     * @param GeoIpMaxMind $geoIpMaxMind
     * @param GeoIpConfig $geoIpConfig
     * @param \Magento\Framework\File\Csv $csvHandle
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpMaxMind $geoIpMaxMind,
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig $geoIpConfig,
        \Magento\Framework\File\Csv $csvHandle,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->timezoneInterface = $timezoneInterface;
        $this->geoIpConfig = $geoIpConfig;
        $this->resourceConnection = $resourceConnection;
        $this->csvHandle = $csvHandle;
        $this->geoIpMaxMind = $geoIpMaxMind;
        $this->file = $file;
    }

    /**
     * @param string $pathImport
     * @return array
     * @throws \Exception
     */
    public function import($pathImport)
    {
        $ext = 'csv';
        $csvFiles = $this->collectFiles($pathImport, $ext);
        $date = $this->timezoneInterface->date()->format('y-m-d H:i:s');
        $result['status'] = 'False';
        $result['time'] = $date;
        try {
            if ($csvFiles) {
                foreach ($csvFiles as $value) {
                    $newFileName = str_replace($pathImport, '', $value);
                    if (strpos($newFileName, 'Blocks-IPv4') !== false) {
                        $result = $this->processImport($value);
                    } elseif (strpos($newFileName, 'Blocks-IPv6') !== false) {
                        $result = $this->processImportIPv6($value);
                    } elseif (strpos($newFileName, 'Locations') !== false) {
                        $result = $this->processImportLocations($value);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \LogicException(__($e->getMessage()));
        }

        return $result;
    }

    /**
     * @param string $beginIp
     * @param string $endIp
     * @param string $countryCode
     * @param string $countryName
     */
    public function saveValue($beginIp, $endIp, $countryCode, $countryName)
    {
        $this->geoIpMaxMind->saveValue($beginIp, $endIp, $countryCode, $countryName);
    }

    /**
     * @param string $newFileName
     * @return array
     * @throws \Exception
     */
    public function processImport($newFileName)
    {
        $result = [];
        $writeAdapter = $this->resourceConnection->getConnection('core_write');
        //Import Direct SQL

        $dataCsv = $this->csvHandle->getData($newFileName);

        $date = $this->timezoneInterface->date()->format('y-m-d H:i:s');
        if ($dataCsv) {
            $countCol = 0;
            $countTotal = 0;
            foreach ($dataCsv as $value) {
                $countTotal++;
            }

            foreach ($dataCsv as $value) {
                $countCol++;
                $range = $this->cidrIPv4ToRange($value[0]);

                //Insert Data into table
                $reviewStoreData = [
                    'network' => $value[0],
                    'geoname_id' => $value[1],
                    'begin_ip' => $range[0],
                    'end_ip' => $range[1]
                ];
                $writeAdapter->insert($this->getTableName('bss_geoip_maxmind_v4'), $reviewStoreData);
                $percent = round((($countCol/$countTotal)*100), 2);
                $percent = (string)$percent;

                if (ctype_digit($percent)) {
                    $this->geoIpConfig->updateValue($percent, 'percent');
                }
            }

            $this->geoIpConfig->deleteValue('last_update');
            $this->geoIpConfig->saveValue($date, 'last_update');

            $result['status'] = 'Done';
            $result['time'] = $date;
        } else {
            $result['status'] = 'False';
            $result['time'] = $date;
        }
        return $result;
    }

    /**
     * @param string $newFileNameIPv6
     * @return array
     * @throws \Exception
     */
    public function processImportIPv6($newFileNameIPv6)
    {
        $result = [];
        $writeAdapter = $this->resourceConnection->getConnection('core_write');
        //Import Direct SQL

        $dataCsvIPv6 = $this->csvHandle->getData($newFileNameIPv6);

        $date = $this->timezoneInterface->date()->format('y-m-d H:i:s');
        if ($dataCsvIPv6) {
            $countCol2 = 0;

            $countTotal = 0;

            foreach ($dataCsvIPv6 as $value) {
                $countTotal++;
            }

            foreach ($dataCsvIPv6 as $value) {
                $countCol2++;
                $range = $this->cidrIPv6ToRange($value[0]);
                //Insert Data into table
                $reviewStoreData = [
                    'network' => $value[0],
                    'geoname_id' => $value[1],
                    'begin_ip' => $range[0],
                    'end_ip' => $range[1]
                ];
                $writeAdapter->insert($this->getTableName('bss_geoip_maxmind_v6'), $reviewStoreData);
                $percent = round((($countCol2/$countTotal)*100), 2);
                $percent = (string)$percent;

                if (ctype_digit($percent)) {
                    $this->geoIpConfig->updateValue($percent, 'percent');
                }
            }

            $this->geoIpConfig->deleteValue('last_update_ipv6');
            $this->geoIpConfig->saveValue($date, 'last_update_ipv6');

            $result['status'] = 'Done';
            $result['time'] = $date;
        } else {
            $result['status'] = 'False';
            $result['time'] = $date;
        }
        return $result;
    }

    /**
     * @param string $newFileName
     * @return array
     * @throws \Exception
     */
    public function processImportLocations($newFileName)
    {
        $result = [];
        $writeAdapter = $this->resourceConnection->getConnection('core_write');
        //Import Direct SQL

        $dataCsv = $this->csvHandle->getData($newFileName);

        $date = $this->timezoneInterface->date()->format('y-m-d H:i:s');
        if ($dataCsv) {
            $countCol = 0;
            $countTotal = 0;

            foreach ($dataCsv as $value) {
                $countTotal++;
            }

            foreach ($dataCsv as $value) {
                $countCol++;
                //Insert Data into table
                $reviewStoreData = [
                    'geoname_id' => $value[0],
                    'locale_code' => $value[1],
                    'continent_code' => $value[2],
                    'continent_name' => $value[3],
                    'country_iso_code' => $value[4]
                ];

                $writeAdapter->insert($this->getTableName('bss_geoip_maxmind_locations'), $reviewStoreData);
                $percent = round((($countCol/$countTotal)*100), 2);
                $percent = (string)$percent;

                if (ctype_digit($percent)) {
                    $this->geoIpConfig->updateValue($percent, 'percent');
                }
            }

            $this->geoIpConfig->deleteValue('last_update');
            $this->geoIpConfig->saveValue($date, 'last_update');

            $result['status'] = 'Done';
            $result['time'] = $date;
        } else {
            $result['status'] = 'False';
            $result['time'] = $date;
        }
        return $result;
    }

    /**
     * @param string $cidr
     * @return mixed
     */
    protected function cidrIPv4ToRange($cidr)
    {
        $cidr = explode('/', $cidr);

        if (!isset($cidr[1])) {
            $range[0] = 0;
            $range[1] = 0;
            return $range;
        }
        
        $start = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[0] = $this->ipv4ToLong($start);
        $end = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        $endNumber = $this->ipv4ToLong($end);
        $ipEnding = (int)$range[0] + (int)$endNumber;
        $range[1] = $ipEnding;
        return $range;
    }

    public function cidrIPv6ToRange($cidr)
    {
        $cidrArray = explode('/', $cidr);
        if (!isset($cidrArray[1])) {
            $range[0] = 0;
            $range[1] = 0;
            return $range;
        }

        list($firstaddrstr, $prefixlen) = $cidrArray;

        // @codingStandardsIgnoreStart
        // Parse the address into a binary string

        $firstaddrbin = inet_pton($firstaddrstr);

        if (!$firstaddrbin) {
            $range[0] = 0;
            $range[1] = 0;
            return $range;
        }

        // Convert the binary string to a string with hexadecimal characters
        $firstaddrhex = unpack('H*', $firstaddrbin);

        // Overwriting first address string to make sure notation is optimal
        $firstaddrstr = inet_ntop($firstaddrbin);

        // Calculate the number of 'flexible' bits
        $flexbits = 128 - (int)$prefixlen;

        // Build the hexadecimal string of the last address
        $lastaddrhex = $firstaddrhex[1];

        // We start at the end of the string (which is always 32 characters long)
        $pos = 31;
        while ($flexbits > 0) {
            // Get the character at this position
            $orig = substr($lastaddrhex, $pos, 1);

            // Convert it to an integer
            $origval = hexdec($orig);

            // OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
            $newval = $origval | (pow(2, min(4, $flexbits)) - 1);

            // Convert it back to a hexadecimal character
            $new = dechex($newval);

            // And put that character back in the string
            $lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

            // We processed one nibble, move to previous position
            $flexbits -= 4;
            $pos -= 1;
        }

        // Convert the hexadecimal string to a binary string
        $lastaddrbin = pack('H*', $lastaddrhex);

        // And create an IPv6 address from the binary string
        $lastaddrstr = inet_ntop($lastaddrbin);
        $range[0] = $this->ipv6ToLong($firstaddrstr);
        $range[1] = $this->ipv6ToLong($lastaddrstr);
        // @codingStandardsIgnoreEnd
        return $range;
    }

    /**
     * @param string $ip
     * @return bool|string
     */
    public function ipv6ToLong($ip)
    {
        // @codingStandardsIgnoreStart
        $pton = inet_pton($ip);
        if (!$pton) {
            return false;
        }
        $number = '';
        foreach (unpack('C*', $pton) as $byte) {
            $number .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }
        return base_convert(ltrim($number, '0'), 2, 10);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @param string $ip
     * @return bool|string
     */
    public function ipv4ToLong($ip)
    {
        $ipArray = explode('.', $ip);
        $ipCustomerLong = (16777216*$ipArray[0])+(65536*$ipArray[1])+(256*$ipArray[2] )+$ipArray[3];
        return $ipCustomerLong;
    }

    /**
     * @param string $entity
     * @return bool|mixed
     */
    public function getTableName($entity)
    {
        if (!isset($this->tableNames[$entity])) {
            try {
                $this->tableNames[$entity] = $this->resourceConnection->getTableName($entity);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->tableNames[$entity];
    }

    /**
     * @param string $dir
     * @param string $ext
     * @return array
     */
    public function collectFiles($dir, $ext)
    {
        if ($this->file->isDirectory($dir) && !empty($this->file->readDirectory($dir))) {
            $files = [];
            foreach ($this->file->readDirectory($dir) as $file) {
                if (preg_match("/\\.$ext\$/i", $file)) {
                    $files[] = $file;
                } else {
                    if ($this->file->isDirectory($file) && $file != '..' && $file != '.') {
                        $result = $this->collectFiles($file, $ext);
                        if ($result) {
                            $files = $this->pushArray($files, $result);
                        }
                    }
                }
            }
            return $files;
        }
        return [];
    }

    /**
     * @param array $array
     * @param array $additionArray
     * @return mixed
     */
    public function pushArray($array, $additionArray)
    {
        foreach ($additionArray as $value) {
            array_push($array, $value);
        }
        return $array;
    }
}
