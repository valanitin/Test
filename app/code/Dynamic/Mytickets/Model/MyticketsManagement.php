<?php
declare(strict_types = 1);

namespace Dynamic\Mytickets\Model;

use Dynamic\Mytickets\Api\MyticketsManagementInterface;
use Exception;
use Magento\Framework\DataObjectFactory;
use function strpos;

class MyticketsManagement implements MyticketsManagementInterface
{
    /**
     * @var array
     */
    public $optionalFields = [
        "customer_id",
        "ticket_type",
        "is_it_bought",
        "cheaper_website",
        "cheaper_link",
        "cheaper_price_with_tax",
        "cheaper_avaliable",
        "country_id"
    ];

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var Mytickets
     */
    protected $mytickets;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param Mytickets         $mytickets
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        Mytickets $mytickets
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->mytickets         = $mytickets;
    }

    /**
     * @inheritDoc
     */
    public function submitForm($ticketForm)
    {

        $result = $this->dataObjectFactory->create();

        $response = [];

        if (empty($ticketForm)) {
            $result->setData('message', 'Please enter required fields.');

            return $result;
        }

        if (empty($ticketForm['name'])) {
            $result->setData('message', 'Enter the name and try again.');

            return $result;
        }
        if (empty($ticketForm['last_name'])) {
            $result->setData('message', 'Enter the last name and try again.');

            return $result;
        }
        if (empty($ticketForm['email'])) {
            $result->setData('message', 'Enter the email and try again.');

            return $result;
        }
        if (false === strpos($ticketForm['email'], '@') || false === strpos($ticketForm['email'], '.com')) {
            $result->setData('message', 'The email address is invalid. Verify the email address and try again.');

            return $result;
        }
        if (empty($ticketForm['phone'])) {
            $result->setData('message', 'Enter the phone and try again.');

            return $result;
        }

        if (empty($ticketForm['brand'])) {
            $result->setData('message', 'Enter the brand and try again.');

            return $result;
        }

        if (empty($ticketForm['style'])) {
            $result->setData('message', 'Enter the style and try again.');

            return $result;
        }
        if (empty($ticketForm['keyword'])) {
            $result->setData('message', 'Enter the keyword and try again.');

            return $result;
        }
        if (empty($ticketForm['image_url'])) {
            $result->setData('message', 'Enter the image url and try again.');

            return $result;
        }

        if (empty($ticketForm['remarks'])) {
            $result->setData('message', 'Enter the remarks and try again.');

            return $result;
        }

        if (empty($ticketForm['lang_code'])) {
            $result->setData('message', 'Enter the lang code and try again.');

            return $result;
        }

        $myTicketsData = [
            "name"      => $ticketForm["name"],
            "last_name" => $ticketForm["last_name"],
            "email"     => $ticketForm["email"],
            "phone"     => $ticketForm["phone"],
            "brand"     => $ticketForm["brand"],
            "style"     => $ticketForm["style"],
            "keyword"   => $ticketForm["keyword"],
            "image"     => $ticketForm["image_url"],
            "remarks"   => $ticketForm["remarks"],
            "lang_code" => $ticketForm["lang_code"],
        ];

        $myTicketsData = $this->addAdditionalFields($ticketForm, $myTicketsData);

        $this->mytickets->setData($myTicketsData);
        try {
            $this->mytickets->save();
            $result->setData('message', 'Request Successfully Sent, You will get Ticket ID shortly.');

            return $result;

        } catch (Exception $e) {
            $result->setData('message', $e->getMessage());

            return $result;
        }
    }

    /**
     *  Adding the Optional params into the Ticket Data
     *
     * @param array $ticketForm
     * @param array $myTicketsData
     *
     * @return mixed
     */
    public function addAdditionalFields($ticketForm, $myTicketsData)
    {
        $optionalFields = $this->optionalFields;
        foreach ($ticketForm as $field => $fieldValue) {
            if (in_array($field, $optionalFields)) {
                $myTicketsData[$field] = $fieldValue;
            }
        }

        return $myTicketsData;
    }
}
