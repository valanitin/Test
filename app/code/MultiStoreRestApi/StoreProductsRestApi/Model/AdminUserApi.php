<?php

namespace MultiStoreRestApi\StoreProductsRestApi\Model;

use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CustomApi
 *
 * @package StoreRestApi\CustomRestApi\Model
 */
class AdminUserApi implements \MultiStoreRestApi\StoreProductsRestApi\Api\AdminUserApiInterface
{
	
	/*
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;
	
	/*
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resourceConnection;
	
	/**
	 * User model factory
	 *
	 * @var \Magento\User\Model\UserFactory
	 */    
	protected $_userFactory;
  
    /*
	 * construct
	 */
	public function __construct(
		Request $request,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\User\Model\UserFactory $userFactory
    ) {       
        $this->request = $request;
		$this->json = $json;
		$this->resourceConnection = $resourceConnection;
		$this->_userFactory = $userFactory;
    }
	
	/**
	 * Create Admin User
	 */
	public function createAdminUser()
	{
		$responseBody = $this->request->getContent();
		$responseJsonDecode = $this->json->unserialize($responseBody);
		
		$response = [];
		if (!array_key_exists('username', $responseJsonDecode)) {
			$response[] = "Parameter 'username' is required";
		} 
		if (!array_key_exists('firstname', $responseJsonDecode)) {
			$response[] = "Parameter 'firstname' is required";
		} 
		if (!array_key_exists('lastname', $responseJsonDecode)) {
			$response[] = "Parameter 'lastname' is required";
		} 
		if (!array_key_exists('email', $responseJsonDecode)) {
			$response[] = "Parameter 'email' is required";
		} 
		if (!array_key_exists('password', $responseJsonDecode)) {
			$response[] = "Parameter 'password' is required";
		}
		
		if(!empty($response)) {
			$response['status'] = "error";
			return json_encode($response);
		}
			
		$adminInfo = [
			'username'  => $responseJsonDecode['username'],
			'firstname' => $responseJsonDecode['firstname'],
			'lastname'  => $responseJsonDecode['lastname'],
			'email'     => $responseJsonDecode['email'],
			'password'  => $responseJsonDecode['password'],
			'is_active' => 1
		];

		
		try{
			$userModel = $this->_userFactory->create();
			$userModel->setData($adminInfo);
			$userModel->setRoleId(1);
			$userModel->save();
			$response['status'] = "success";
			$response[] = "Admin user created successfully.";
		} catch (\Exception $ex) {
			$response['status'] = "error";
			$response[] = $ex->getMessage();
		}		
		return json_encode($response);		
	}
	
	/**
	 * Delete Admin User
	 */
	public function deleteAdminUser()
	{
		$responseBody = $this->request->getContent();
		$responseJsonDecode = $this->json->unserialize($responseBody);
		
		$response = [];
		if (!array_key_exists('username', $responseJsonDecode)) {
			$response[] = "Parameter 'username' is required";
		}
		
		if(!empty($response)) {
			$response['status'] = "error";
			return json_encode($response);
		}
		
		try{
			$userModel = $this->_userFactory->create();
			$userModel->load($responseJsonDecode['username'],'username');
			if($userModel->getId()) {
				$userModel->delete();
				$response['status'] = "success";
				$response[] = "Admin user deleted successfully.";
			} else {
				$response['status'] = "error";
				$response[] = "Admin User not found with given username.";
			}			
		} catch (\Exception $ex) {
			$response['status'] = "error";
			$response[] = $ex->getMessage();
		}		
		return json_encode($response);
	}
	
	/**
	 * Edit Admin User
	 */
	public function editAdminUser()
	{
		$responseBody = $this->request->getContent();
		$responseJsonDecode = $this->json->unserialize($responseBody);
		
		$response = [];
		if (!array_key_exists('username', $responseJsonDecode)) {
			$response[] = "Parameter 'username' is required";
		}

		if(!empty($response)) {
			$response['status'] = "error";
			return json_encode($response);
		}
		
		$firstName = $lastName = $email = $password = $isActive = "";
		if (array_key_exists('firstname', $responseJsonDecode)) {
			$firstName = $responseJsonDecode['firstname'];
		} 
		if (array_key_exists('lastname', $responseJsonDecode)) {
			$lastName = $responseJsonDecode['lastname'];
		} 
		if (array_key_exists('email', $responseJsonDecode)) {
			$email = $responseJsonDecode['email'];
		} 
		if (array_key_exists('password', $responseJsonDecode)) {
			$password = $responseJsonDecode['password'];
		}
		if (array_key_exists('is_active', $responseJsonDecode)) {
			$isActive = $responseJsonDecode['is_active'];
		}
		
		$username = $responseJsonDecode['username'];
		
		try{
			$userModel = $this->_userFactory->create();
			$userModel->load($responseJsonDecode['username'],'username');
			if($userModel->getId()) {
				if($firstName) {
					$userModel->setData('firstname', $firstName);
				}
				if($lastName) {
					$userModel->setData('lastname', $lastName);
				}
				if($email) {
					$userModel->setData('email', $email);
				}
				if($password) {
					$userModel->setData('password', $password);
				}
				if($isActive === 0 || $isActive === 1) {
					$userModel->setData('is_active', $isActive);
				}
				$userModel->save();
				$response['status'] = "success";
				$response[] = "Admin user edited successfully.";
			} else {
				$response['status'] = "error";
				$response[] = "Admin User not found with given username.";
			}
		} catch (\Exception $ex) {
			$response['status'] = "error";
			$response[] = $ex->getMessage();
		}		
		return json_encode($response);		
	}
	
}