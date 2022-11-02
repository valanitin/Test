<?php
namespace Ethnic\Customfooter\Api\Data;

interface AllnewsInterface
{
	const ID = 'id';
	const ENABLED = 'enabled';
	const TITLE  = 'title';
	const CMS_PAGE_ID = 'cms_page_id';
	const IS_PARENT = 'is_parent';
	const PARENT_ID = 'parent_id';

	public function getId();

	public function getEnabled();

	public function getTitle();

	public function getcmsPageId();

	public function getIsParent();

	public function getParentId();

	public function setId($id);

	public function setEnabled($enabled);

	public function setTitle($title);

	public function setCmsPageId($cms_page_id);

	public function setIsParent($is_parent);

	public function setParentId($parent_id);

}
?>
