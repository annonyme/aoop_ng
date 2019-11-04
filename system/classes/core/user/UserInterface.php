<?php
namespace core\user;

interface UserInterface{
	
	/**
	 * @return int
	 */
	public function getId();
	
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @return string
	 */
	public function getEmail();
	
	/**
	 * @return bool
	 * @param string $groupName
	 */
	public function isInGroup($groupName);
}