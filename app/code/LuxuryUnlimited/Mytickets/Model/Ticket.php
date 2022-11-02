<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Mytickets\Model;

use Dynamic\Mytickets\Model\Mytickets;
use Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory;
use Exception;
use LuxuryUnlimited\Mytickets\Api\TicketInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;

class Ticket implements TicketInterface
{
    const TABLE = 'mytickets';

    /*
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /*
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $Json;

    /*
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $myTickets;

    /*
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var CollectionFactory
     */
    protected $myTicketCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @param LoggerInterface $logger
     * @param Json $json
     * @param Mytickets $myTickets
     * @param Request $request
     * @param ResourceConnection $resource
     * @param CollectionFactory $myTicketCollectionFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Curl $curl
     */
    public function __construct(
        LoggerInterface $logger,
        Json $json,
        Mytickets $myTickets,
        Request $request,
        ResourceConnection $resource,
        CollectionFactory $myTicketCollectionFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Curl $curl
    ) {
        $this->logger = $logger;
        $this->json = $json;
        $this->myTickets = $myTickets;
        $this->request = $request;
        $this->resource = $resource;
        $this->myTicketCollectionFactory = $myTicketCollectionFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_curl = $curl;
    }

    /**
     * Get Ticket Data
     *
     * @return string
     */
    public function getTicket()
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from(
                ['c' => $connection->getTableName(self::TABLE)],
                ['*']
            );
        $result = [];
        $data = $connection->fetchAll($select);
        foreach ($data as $key => $value) {
            if (!empty($value["messages"])) {
                $message = $this->json->unserialize($value["messages"]);
                $value["messages"] = $message;
                $result[$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Get Ticket Data
     *
     * @return string[]
     */
    public function getMyTicket()
    {
        $customerId = $this->request->getParam('customerId');
        if ($customerId == null) {
            return ['error' => false, 'message' => 'Customer id not found in the url'];
        }
        if (!is_numeric($customerId)) {
            return ['error' => false, 'message' => 'Customer id not correct'];
        }

        $customerEmail = $this->getCustomerEmail($customerId);

        $myTicketCollection = $this->myTicketCollectionFactory->create();
        if ($customerEmail) {
            $myTicketCollection->addFieldToFilter('email', ['eq' => $customerEmail]);
        } else {
            $myTicketCollection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        }
        $myTicketCollection->setOrder('mytickets_id','DESC');
        $result = [];
        $ticketData = $myTicketCollection->getData();
        if (empty($ticketData)) {
            $result[] = ['error' => false, 'message' => 'No tickets Found'];
        }
        foreach ($ticketData as $key => $value) {
            if (!empty($value["messages"])) {
                $message = $this->json->unserialize($value["messages"]);
                $value["messages"] = $message;
            }
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getCustomerEmail($customerId): string
    {
        try {
            $customer = $this->_customerRepositoryInterface->getById($customerId);
        } catch (\Exception $exception) {
            return '';
        }
        return $customer->getEmail();
    }

    /**
     * Update Ticket Data
     *
     * @return string
     */
    public function updateTicket()
    {
        try {
            $this->logger->info('-- Update Ticket Api Call --');
            $result = [];
            $data = $this->request->getBodyParams();
            if (isset($data['mytickets_id'])) {
                $myTicket = $this->myTickets->load($data['mytickets_id'], "mytickets_id");
                $message = $this->json->serialize($data["messages"]);
                $data["messages"] = $message;
                $myTicket->setData($data);
                $myTicket->save();
                $result[] = [
                    'status' => "Success",
                    'message' => "Ticket Updated"
                ];

                return $result;
            } else {
                $result[] = [
                    'status' => "Error",
                    'message' => "Params are missing or incorrect to update the Ticket"
                ];

                return $result;
            }
        } catch (Exception $e) {
            $this->logger->info("Update Ticket Api call---" . $e);
            $result[] = ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }

    /**
     * @return array
     */
    public function getMyTicketByEmail(): array
    {
        $customerEmail = $this->request->getParam('customerEmail');
        $result = [];
        if ($customerEmail == null) {
            return ['error' => false, 'message' => 'Customer Email not found in the url'];
        }
        if (strpos($customerEmail, '@') === false) {
            return ['error' => false, 'message' => 'Customer Email not correct'];
        }

        $myTicketCollection = $this->myTicketCollectionFactory->create();
        $myTicketCollection->addFieldToFilter('email', ['eq' => $customerEmail]);
        $myTicketCollection->setOrder('mytickets_id','DESC');
        $ticketData = $myTicketCollection->getData();
        if (empty($ticketData)) {
            $result[] = ['error' => false, 'message' => 'No tickets Found'];
        }
        foreach ($ticketData as $key => $value) {
            if ($value['ticket_code'])
            {
                $erpTicketMessages = $this->getErpTicketMessageByCode($value['ticket_code'], $customerEmail);
                if(!empty($erpTicketMessages))
                {
                    $value["messages"] = $erpTicketMessages;
                }
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * @param $ticketCode
     * @param $customerEmail
     * @return mixed|string
     */
    protected function getErpTicketMessageByCode($ticketCode, $customerEmail)
    {
        $url = "https://erp.theluxuryunlimited.com/api/ticket/send";
        $newjsonData = array(
            'website' => 'www.sololuxury.com',
            'email' => $customerEmail,
            'ticket_id' => $ticketCode
        );
        $params = json_encode($newjsonData);
        $this->_curl->setOption(CURLOPT_POST, true);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $headers = ["Content-Type" => "application/json"];
        $this->_curl->setHeaders($headers);
        $this->_curl->post($url, $params);
        $data = json_decode($this->_curl->getBody(), true);
        if (empty($data)) {
            $apiTicketsMessage = "";
        } else {
            $apiTickets = $data['tickets'];
            $apiTicketsData = $apiTickets['data'][0];
            $apiTicketsMessage = $apiTicketsData['messages'];
        }
        return $apiTicketsMessage;
    }
}
