<?php
declare(strict_types=1);

namespace Dynamic\ContactformApi\Model;

use Magento\Contact\Model\MailInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\ContactformApi\Api\ContactusManagementInterface;

/**
 * Class ContactusManagement
 *
 */
class ContactusManagement implements ContactusManagementInterface
{ 
    private $mail;

    protected $dataObjectFactory;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    public function __construct(
        MailInterface $mail,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) {
        $this->mail = $mail;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->encoder = $encoder;
        $this->curlFactory = $curlFactory;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($contactForm) {

        $result = $this->dataObjectFactory->create();

        if (empty($contactForm['name'])) {
            $result->setData('message', 'Enter the Name and try again.');
            return $result;
        }
        if (empty($contactForm['lastname'])) {
            $result->setData('message', 'Enter the lastname and try again.');
            return $result;
        }
        if (empty($contactForm['subject'])) {
            $result->setData('message', 'Enter the subject and try again.');
            return $result;
        }
        if (empty($contactForm['email'])) {
            $result->setData('message', 'Enter the Email and try again.');
            return $result;
        }
        if (false === \strpos($contactForm['email'], '@') || false === \strpos($contactForm['email'], '.com')) {
            $result->setData('message', 'The email address is invalid. Verify the email address and try again.');
            return $result;
        }
        if (empty($contactForm['country'])) {
            $result->setData('message', 'Enter the country and try again.');
            return $result;
        }
        if (empty($contactForm['type_of_inquiry'])) {
            $result->setData('message', 'Enter the type of inquiry and try again.');
            return $result;
        }

        try {
            /* api call */
            $url = 'https://erp.theluxuryunlimited.com/api/ticket/create';
            $data = [
                    "name" => $contactForm['name'],
                    "last_name" => $contactForm['lastname'],
                    "email" => $contactForm['email'],
                    "type_of_inquiry" => $contactForm['type_of_inquiry'],
                    "order_no" => $contactForm['order_no'],
                    "country" => $contactForm['country'],
                    "subject" => $contactForm['subject'],
                    "message" => $contactForm['message'],
                    "source_of_ticket" => $contactForm['source_of_ticket'],
                    "phone_no" => $contactForm['phone_no'],
                ];

           
            $paramsData = $this->encoder->encode($data);
            
            $curl = $this->curlFactory->create();

            $curl->setOption(CURLOPT_RETURNTRANSFER, 1);

            $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            
            try{

                $data = [];

                $curl->post($url, $paramsData);

                $result = $curl->getBody();

                $response = array(json_decode($result,true));
                return $response;

            }catch(Exception $e){
                
                $data = array(
                    [   'status' => 'Error',
                        'message' => $e->getMessage()
                    ]
                );
                
                return $data;
            }

            /* api call end */
            $this->sendEmail($contactForm);
            $result->setData('message', 'Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.');
        } catch (LocalizedException $e) {
            $result->setData('message', $e->getMessage());
        } catch (\Exception $e) {
            $result->setData('message', 'An error occurred while processing your form. Please try again later.');
        }
        return $result;
    }

    /**
     * @param array $post Post data from contact form
     * @return void
     */
    private function sendEmail($post)
    {
        $this->mail->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }
}