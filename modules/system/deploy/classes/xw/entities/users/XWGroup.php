<?php
/*
 * Created on 27.05.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
 * Copyright (c) 2007/2009/2015 Hannes Pries <http://www.annonyme.de>
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace xw\entities\users;

//rolebased rightsmanagment
use core\user\UserInterface;

class XWGroup
{
    private $id = 0;
    private $name = "";
    private $description = "";

    private $userList = [];

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    //----

    public function save()
    {
        $dao = XWUserManagmentDAO::instance();
        $dao->saveGroup($this);
    }

    public function load($id)
    {
        $this->userList = [];
        $dao = XWUserManagmentDAO::instance();
        $group = $dao->loadGroup($id);
        $this->id = $group->getId();
        $this->name = $group->getName();
        $this->description = $group->getDescription();

        for ($i = 0; $i < $group->getSize(); $i++) {
            $this->addUser($group->getUser($i));
        }
    }

    public function delete()
    {
        $dao = XWUserManagmentDAO::instance();
        $dao->deleteGroup($this);
    }

    /**
     * @deprecated
     */
    public function addUserToGroup($userId)
    {
        $user = new XWUser();
        $user->load($userId);
        $dao = XWUserManagmentDAO::instance();
        $dao->saveUserToGroup($this, $user);
    }

    //0.3.2 update
    public function saveUserTo($user)
    {
        $dao = XWUserManagmentDAO::instance();
        $dao->saveUserToGroup($this, $user);
    }

    /**
     * @deprecated
     */
    public function removeUserFromGroup($userId)
    {
        $user = new XWUser();
        $user->load($userId);
        $dao = XWUserManagmentDAO::instance();
        $dao->removeUserFromGroup($this, $user);
    }

    /**
     * @deprecated
     */
    public function removeUser($user)
    {
        $this->removeUserFrom($user);
    }

    //0.3.5.4 update
    public function removeUserFrom($user)
    {
        $dao = XWUserManagmentDAO::instance();
        $dao->removeUserFromGroup($this, $user);
    }

    //-----

    public function addUser($user)
    {
        $this->userList[count($this->userList)] = $user;
    }

    public function getSize()
    {
        return count($this->userList);
    }

    public function getUser($index)
    {
        return $this->userList[$index];
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function existsIn($user)
    {
        $dummy = null;
        $found = false;
        if ($user->getId() > 0) {
            for ($i = 0; $i < $this->getSize(); $i++) {
                $dummy = $this->getUser($i);
                if ($dummy->getId() == $user->getId()) {
                    $found = true;
                    return $found;
                }
            }
        }
        return $found;
    }
}
