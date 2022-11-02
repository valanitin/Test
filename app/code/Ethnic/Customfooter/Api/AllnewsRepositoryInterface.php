<?php
namespace Ethnic\Customfooter\Api;

interface AllnewsRepositoryInterface
{
	public function save(\Ethnic\Customfooter\Api\Data\AllnewsInterface $news);

    public function getById($enabledId);

    public function delete(\Ethnic\Customfooter\Api\Data\AllnewsInterface $news);

    public function deleteById($enabledId);

    public function getData($id);

}
?>