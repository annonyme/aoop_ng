<?php
namespace  core\menu;

use core\user\UserInterface;

class MenuBuilder{
	/**
	 * @var MenuItem[]
	 */
	private $items = [];
	private $renderer = null;
	
	/**
	 * @param array $items
	 * @param MenuRendererInterface $renderer
	 */
	public function __construct($items = [], $renderer = null){
		$this->items = $items;
		$this->renderer = $renderer;
	}
	
	/**
	 * @param MenuItem $item
	 */
	public function addMenuItem($item){
		$this->items[] = $item;
	}
	
	/**
	 * @return MenuItem[]
	 */
	public function getItems() {
		return $this->items;
	}
	
	/**
	 * @return MenuItem[]
	 * @param MenuItem[] $items
	 */
	public function sortItems($items){
		$tmp = [];
		foreach ($items as $item){
			if(!isset($tmp[$item->getOrderGroup()])){
				$tmp[$item->getOrderGroup()] = [];
			}
			if(count($item->getSubItems()) > 0){
				$subItems = $this->sortItems($item->getSubItems());
				$item->setSubItems($subItems);
			}
			if(!isset($tmp[$item->getOrderGroup()][$item->getName()])){
				$tmp[$item->getOrderGroup()][$item->getName()] = [];
			}
			$tmp[$item->getOrderGroup()][$item->getName()][] = $item;
		}
		ksort($tmp);
		$final = [];
		foreach ($tmp as $group){
			ksort($group);
			foreach ($group as $nameGroup){
				foreach ($nameGroup as $item){
					$final[] = $item;
				}
			}
		}		
		return $final;
	}
	
	/**
	 * @param MenuRendererInterface $renderer
	 */
	public function setRenderer($renderer){
		$this->renderer = $renderer;
	}
	
	/**
	 * @return string
	 * @param MenuItem[] $items
	 * @param UserInterface|null $user
	 */
	private function filterForUser($items = [], $user = null){
		$tmp = [];
		foreach ($this->items as $item){
			if((count($item->getUserGroups()) == 0 && !$item->isOnlyLoggedIn()) 
					|| ($item->isOnlyLoggedIn() && $user !== null)
					|| ($user !==null && $this->checkUserGroups($user, $items->getUserGroups()))){
				$item->setSubItems($this->filterForUser($item->getSubItems(), $user));
				$tmp[] = $item;
			}
		}
		return $tmp;
	}
	
	/**
	 * @return bool
	 * @param UserInterface $user
	 * @param array $groups
	 */
	private function checkUserGroups($user, $groups = []){
		$result = false;
		foreach ($groups as $group){
			if($user->isInGroup($group)){
				$result = true;
			}
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @param bool $sortItems
	 * @param UserInterface|null $user
	 */
	public function render($sortItems = false, $user = null){
		if($sortItems){
			$this->items = $this->sortItems($this->items);
		}
		$this->items = $this->filterForUser($this->items, $user);
		$result = "";
		if($this->renderer !== null){
			
		}
		else{
			throw new \Exception("no menu renderer setted");
		}
		return $result;
	}	
}