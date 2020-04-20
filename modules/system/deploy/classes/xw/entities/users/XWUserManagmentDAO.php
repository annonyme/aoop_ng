<?php
/*
 * Created on 27.05.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
 * Copyright (c) 2007/2010/2011/2012/2014 Hannes Pries <http://www.annonyme.de>
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

use core\security\XWScramblingToolKit;
use core\security\XWHashCreator;
use core\user\UserInterface;
use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use core\database\XWSQLStatement;
use core\database\XWSQLSecure;
use core\database\XWSearchStringParser;

class XWUserManagmentDAO
{
    private $db = null;

    static private $instance = null;

    static public function instance()
    {
        if (self::$instance == null) {
            self::$instance = new XWUserManagmentDAO();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $dbName = XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
        $this->db = PDBCCache::getInstance()->getDB($dbName);
    }

    //------ Users ---------------

    public function loadUser($id)
    {
        $sql = "SELECT U.USER_ID, " .
            "      U.USER_NAME, " .
            "      U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_USELOGINLOG, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_ID=" . intval($id);
        $db = $this->db;
        $db->executeQuery($sql);
        $user = new XWUser();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $user->setUseLoginLog($db->getResult($i, "USER_USELOGINLOG"));
        }
        return $user;
    }

    public function loadUserByName($name)
    {
        //$security=new XWSQLSecure();
        //$userName=$security->removeSingleQuotes($userName);

        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_USELOGINLOG, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_NAME=#{name} ";

        $stmt = new XWSQLStatement($sql);
        $stmt->setString("name", $name);

        $db = $this->db;
        $db->executeQuery($stmt->getSQL());
        $user = new XWUser();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $user->setUseLoginLog($db->getResult($i, "USER_USELOGINLOG"));
        }
        return $user;
    }

    /**
     * @param UserInterface $user
     * @param $password
     */
    public function saveUser($user, $password)
    {
        $activeInt = 0;
        if ($user->isActive()) {
            $activeInt = 1;
        }

        $useLoginLogInt = 0;
        if ($user->isUseLoginLog()) {
            $useLoginLogInt = 1;
        }
        $db = $this->db;
        $tk = new XWScramblingToolKit();

        $stmt = new XWSQLStatement("");
        if ($user->getId() != 0) {
            $sql = "UPDATE XW_USERS SET " .
                "  USER_NAME=#{name}, " .
                "  USER_EMAIL=#{email}, " .
                "  USER_ACTIVE=#{active}, " .
                "  USER_USELOGINLOG=#{loginlog}, " .
                "  USER_PMPOPUP=0 " .
                "WHERE USER_ID=#{id} ";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("name", $user->getName());
            $stmt->setString("email", $tk->simpleScrambling($user->getEmail()));
            $stmt->setInt("active", $activeInt);
            $stmt->setInt("loginlog", $useLoginLogInt);
            $stmt->setInt("id", $user->getId());

        } else {
            $hc = new XWHashCreator();
            $sql = "INSERT INTO XW_USERS(USER_NAME,USER_EMAIL,USER_PASSWORD,USER_ACTIVE,USER_REGISTRATIONDATE,USER_USESHIGHSECPASSWORD)" .
                " VALUES (#{name},#{email},'" . $hc->createBCrypt($password) . "',1,CURRENT_TIMESTAMP,2)";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("name", $user->getName());
            $stmt->setString("email", $tk->simpleScrambling($user->getEmail()));
        }
        $db->execute($stmt->getSQL());
    }

    public function deleteUser($user)
    {
        $sql = "UPDATE XW_USERS SET USER_ACTIVE=0 WHERE USRER_ID=" . $user->getId();
        $db = $this->db;
        $db->execute($sql);
    }

    private function checkHighLevelSecurityPassword($userName, $password)
    {
        $found = false;
        $hc = new XWHashCreator();

        $sql = "SELECT COUNT(*) CNT " .
            "FROM XW_USERS U " .
            "WHERE U.USER_NAME=#{name} " .
            "  AND (USER_PASSWORD='" . md5($password) . "' " .
            "   OR USER_PASSWORD='" . $hc->userNameAsSaltEncodeDoubleHash($password, $userName) . "')" .
            "  AND USER_USESHIGHSECPASSWORD<2 " .
            "  AND USER_ACTIVE=1";

        $stmt = new XWSQLStatement($sql);
        $stmt->setString("name", $userName);

        $db = $this->db;
        $db->executeQuery($stmt->getSQL());
        for ($i = 0; $i < $db->getCount(); $i++) {
            $found = $db->getResult($i, "CNT") > 0;
        }

        if ($found) {

            $sql = "UPDATE XW_USERS SET " .
                "  USER_PASSWORD='" . $hc->createBCrypt($password) . "', " .
                "  USER_USESHIGHSECPASSWORD=2 " .
                "WHERE USER_NAME=#{name}";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("name", $userName);
            $db->execute($stmt->getSQL());
        }
    }

    public function loginUser($userName, $password)
    {
        $security = new XWSQLSecure();
        $userName = $security->removeSingleQuotes($userName);

        $this->checkHighLevelSecurityPassword($userName, $password);

        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_USELOGINLOG, " .
            "       U.USER_PASSWORD, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_NAME=#{name} " .
            "  AND USER_ACTIVE=1";

        $stmt = new XWSQLStatement($sql);
        $stmt->setString("name", $userName);

        $db = $this->db;
        $db->executeQuery($stmt->getSQL());
        $user = new XWUser();
        $found = false;

        for ($i = 0; $i < $db->getCount(); $i++) {
            if (XWHashCreator::validateBCrypt($password, $db->getResult($i, "USER_PASSWORD"))) {
                $found = true;
            }
        }

        if ($found) {
            $tk = new XWScramblingToolKit();
            for ($i = 0; $i < $db->getCount(); $i++) {
                $user->setId($db->getResult($i, "USER_ID"));
                $user->setName($db->getResult($i, "USER_NAME"));
                $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
                $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
                $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
                $user->setUseLoginLog($db->getResult($i, "USER_USELOGINLOG"));
            }
        }

        //check if SQLSEC_CHECK EQUALS USER_NAME!

        return $user;
    }

    /**
     * @param XWuser $user
     * @param string $old
     * @param string $new
     *
     * @return boolean
     */
    public function changePasswordUser($user, $old, $new)
    {
        $db = $this->db;
        $check = $this->loginUser($user->getName(), $old);
        $result = false;
        if ($check->getId() > 0) {
            $sql = "UPDATE XW_USERS SET " .
                "  USER_PASSWORD='" . XWHashCreator::createBCrypt($new) . "', " .
                "  USER_USESHIGHSECPASSWORD=2 " .
                "WHERE USER_ID=" . intval($user->getId());
            $db->execute($sql);
            $result = true;
        }

        return $result;
    }

    /**
     * @param XWUser $user
     * @param string $new
     *
     * @return boolean
     */
    public function changeUserPasswordByAdmin($user, $new)
    {
        $db = $this->db;

        $sql = "UPDATE XW_USERS SET " .
            "  USER_PASSWORD='" . XWHashCreator::createBCrypt($new) . "', " .
            "  USER_USESHIGHSECPASSWORD=2 " .
            "WHERE USER_ID=" . intval($user->getId());
        $db->execute($sql);

        return $this->loginUser($user->getName(), $new)->getId() > 0;
    }

    public function loadUserList($active = true)
    {
        $activeInt = 0;
        if ($active) {
            $activeInt = 1;
        }
        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_ACTIVE=" . $activeInt . " " .
            "ORDER BY U.USER_NAME ";
        $db = $this->db;
        $db->executeQuery($sql);
        $list = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $list->addUser($user);
        }
        return $list;
    }

    public function loadUserListByFirstLetter($letter = "a", $active = true)
    {
        $activeInt = 0;
        if ($active) {
            $activeInt = 1;
        }

        $parser = new XWSearchStringParser();
        $letter = $parser->userNameCleaning($letter);

        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_ACTIVE=" . $activeInt . " " .
            "   AND (U.USER_NAME LIKE #{patternU} " .
            "        OR U.USER_NAME LIKE #{patternL})" .
            "ORDER BY U.USER_NAME ";

        $stmt = new XWSQLStatement($sql);
        $stmt->setStringWithWildcards("patternU", strtoupper($letter), false, true);
        $stmt->setStringWithWildcards("patternL", strtolower($letter), false, true);

        $db = $this->db;
        $db->executeQuery($stmt->getSQL());
        $list = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $list->addUser($user);
        }
        return $list;
    }

    public function loadUserListByPattern($letter = "a", $active = true)
    {
        $activeInt = 0;
        if ($active) {
            $activeInt = 1;
        }

        $parser = new XWSearchStringParser();
        $letter = $parser->userNameCleaning($letter);

        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_ACTIVE=" . $activeInt . " " .
            "   AND (LOWER(U.USER_NAME) LIKE #{patternL})" .
            "ORDER BY U.USER_NAME ";

        $stmt = new XWSQLStatement($sql);
        $stmt->setStringWithWildcards("patternL", strtolower($letter), true, true);

        $db = $this->db;
        $db->executeQuery($stmt->getSQL());
        $list = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $list->addUser($user);
        }
        return $list;
    }

    public function loadUserListByPageAndCount($page = 0, $count = 20, $active = true)
    {
        $activeInt = 0;
        if ($active) {
            $activeInt = 1;
        }
        $sql = "SELECT U.USER_ID, " .
            "       U.USER_NAME, " .
            "       U.USER_EMAIL, " .
            "       U.USER_ACTIVE, " .
            "       U.USER_PMPOPUP, " .
            "       U.USER_REGISTRATIONDATE " .
            "FROM XW_USERS U " .
            "WHERE U.USER_ACTIVE=" . $activeInt . " " .
            "ORDER BY U.USER_NAME ASC " .
            "LIMIT " . (intval($page) * intval($count)) . "," . ((intval($page) * intval($count)) + intval($count)) . "";

        $db = $this->db;
        $db->executeQuery($sql);
        $list = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $user->setRegistrationDate($db->getResult($i, "USER_REGISTRATIONDATE"));
            $list->addUser($user);
        }
        return $list;
    }


    //------ Groups ---------------
    public function loadGroup($id)
    {
        $sql = "SELECT G.GROUP_ID, G.GROUP_NAME, G.GROUP_DESCRIPTION " .
            "FROM XW_GROUPS G " .
            "WHERE G.GROUP_ID=" . intval($id);
        $db = $this->db;
        $db->executeQuery($sql);
        $group = new XWGroup();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $group->setId($db->getResult($i, "GROUP_ID"));
            $group->setName($db->getResult($i, "GROUP_NAME"));
            $group->setDescription($db->getResult($i, "GROUP_DESCRIPTION"));
        }

        $sql = "SELECT U.USER_ID, U.USER_NAME, U.USER_EMAIL, " .
            "       U.USER_ACTIVE, U.USER_PMPOPUP " .
            "FROM XW_USERS U, XW_USERS_GROUPS UG " .
            "WHERE UG.GROUP_ID=" . intval($group->getId()) . " " .
            "  AND U.USER_ID=UG.USER_ID " .
            "  AND U.USER_ACTIVE=1 ";
        $db->executeQuery($sql);
        $user = null;
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $group->addUser($user);
        }
        return $group;
    }


    public function loadGroupList()
    {
        $sql = "SELECT G.GROUP_ID, G.GROUP_NAME, G.GROUP_DESCRIPTION " .
            "FROM XW_GROUPS G " .
            "ORDER BY G.GROUP_NAME";
        $db = $this->db;
        $db->executeQuery($sql);
        $groups = new XWGroupList();
        $group = null;
        for ($i = 0; $i < $db->getCount(); $i++) {
            $group = new XWGroup();
            $group->setId($db->getResult($i, "GROUP_ID"));
            $group->setName($db->getResult($i, "GROUP_NAME"));
            $group->setDescription($db->getResult($i, "GROUP_DESCRIPTION"));
            $groups->addGroup($group);
        }
        return $groups;
    }

    /**
     * @param XWGroup $group
     *
     * @return XWUserList
     */
    public function loadUserListByGroup($group)
    {
        $sql = "SELECT U.USER_ID, U.USER_NAME, U.USER_EMAIL, " .
            "      U.USER_ACTIVE, U.USER_PMPOPUP " .
            "FROM XW_USERS U, XW_USERS_GROUPS UG " .
            "WHERE UG.GROUP_ID=" . intval($group->getId()) . " " .
            "  AND U.USER_ID=UG.USER_ID " .
            "  AND U.USER_ACTIVE=1 ";
        $db = $this->db;
        $db->executeQuery($sql);
        $userList = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $userList->addUser($user);
        }
        return $userList;
    }

    /**
     * @param UserInterface $user
     *
     * @return XWGroupList
     */
    public function loadGroupsOfUser($user)
    {
        $db = $this->db;
        $sql = "SELECT G.GROUP_ID, G.GROUP_NAME, G.GROUP_DESCRIPTION " .
            "FROM XW_GROUPS G, XW_USERS_GROUPS UG " .
            "WHERE UG.USER_ID=" . intval($user->getId()) . " " .
            "  AND G.GROUP_ID=UG.GROUP_ID";
        $db = $this->db; //php4?
        $db->executeQuery($sql);
        $groups = new XWGroupList();
        $group = null;
        for ($i = 0; $i < $db->getCount(); $i++) {
            $group = new XWGroup();
            $group->setId($db->getResult($i, "GROUP_ID"));
            $group->setName($db->getResult($i, "GROUP_NAME"));
            $group->setDescription($db->getResult($i, "GROUP_DESCRIPTION"));
            $groups->addGroup($group);
        }
        return $groups;
    }

    /**
     * @param XWGroup $group
     * @param UserInterface $user
     */
    public function saveUserToGroup($group, $user)
    {
        $db = $this->db;
        $sql = "INSERT INTO XW_USERS_GROUPS(GROUP_ID, USER_ID) " .
            "VALUES (" . intval($group->getId()) . "," . intval($user->getId()) . ")";
        $db->execute($sql);
    }

    /**
     * @param XWGroup $group
     * @param UserInterface $user
     */
    public function removeUserFromGroup($group, $user)
    {
        $db = $this->db;
        $sql = "DELETE FROM XW_USERS_GROUPS " .
            "WHERE GROUP_ID=" . intval($group->getId()) . " AND USER_ID=" . intval($user->getId());
        $db->execute($sql);
    }

    /**
     * @param XWGroup $group
     */
    public function saveGroup($group)
    {
        $db = $this->db;
        if ($group->getId() == 0) {
            $sql = "INSERT INTO XW_GROUPS (GROUP_NAME,GROUP_DESCRIPTION) " .
                "VALUES (#{name},#{description})";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("name", $group->getName());
            $stmt->setString("description", $group->getDescription());

            $db->execute($stmt->getSQL());
        } else {
            $sql = "UPDATE XW_GROUPS SET " .
                " GROUP_NAME=#{name}, " .
                " GROUP_DESCRIPTION=#{description} " .
                "WHERE GROUP_ID=#{id}";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("name", $group->getName());
            $stmt->setString("description", $group->getDescription());
            $stmt->setInt("id", $group->getId());

            $db->execute($stmt->getSQL());
        }
    }

    /**
     * @param XWGroup $group
     */
    public function deleteGroup($group)
    {
        $db = $this->db;
        $sql = "DELETE FROM XW_USERS_GROUPS WHERE GROUP_ID=" . intval($group->getId());
        $db->execute($sql);
        $sql = "DELETE FROM XW_GROUPS WHERE GROUP_ID=" . intval($group->getId());
        $db->execute($sql);
    }

    //------ Friends ---------------
    //user say :this is my friend.
    //friend asks: i'm a friend of user?
    /**
     * @param UserInterface $user
     * @param UserInterface $friend
     */
    public function addFriend($user, $friend)
    {
        $db = $this->db;
        $sql = "INSERT INTO XW_FRIENDS(USER_ID,FRIEND_ID) VALUES (" . intval($user->getId()) . "," . intval($friend->getId()) . ")";
        $db->execute($sql);
    }

    /**
     * @param UserInterface $user
     * @param UserInterface $friend
     */
    public function removeFriend($user, $friend)
    {
        $db = $this->db;
        $sql = "DELETE FROM XW_FRIENDS WHERE USER_ID=" . intval($user->getId()) . " AND FRIEND_ID=" . intval($friend->getId()) . "";
        $db->execute($sql);
    }

    /**
     * @param UserInterface $user
     * @param UserInterface $friend
     *
     * @return bool
     */
    public function isUserFriend($user, $friend)
    {
        if ($user->getId() == $friend->getId()) {
            return true; //ever you are your own friend
        } else {
            $db = $this->db;
            $sql = "SELECT COUNT(*) FR_CNT FROM XW_FRIENDS WHERE USER_ID=" . intval($user->getId()) . " AND FRIEND_ID=" . intval($friend->getId());
            $db->executeQuery($sql);
            $count = 0;
            for ($i = 0; $i < $db->getCount(); $i++) {
                $count = $db->getResult($i, "FR_CNT");
            }
            return $count > 0;
        }
    }

    /**
     * @param UserInterface $user
     *
     * @return XWUserList
     */
    public function loadFriendsOfUser($user)
    {
        $sql = "SELECT U.USER_ID, U.USER_NAME, U.USER_EMAIL, " .
            "      U.USER_ACTIVE, U.USER_PMPOPUP " .
            "FROM XW_USERS U, XW_FRIENDS F " .
            "WHERE F.USER_ID=" . intval($user->getId()) . " " .
            "  AND U.USER_ID=F.FRIEND_ID";
        $db = $this->db;
        $db->executeQuery($sql);
        $userList = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $userList->addUser($user);
        }
        return $userList;
    }

    /**
     * @param UserInterface $user
     *
     * @return XWUserList
     */
    public function loadUsersWhoAddedUserAsFriend($user)
    {
        $sql = "SELECT U.USER_ID, U.USER_NAME, U.USER_EMAIL, " .
            "      U.USER_ACTIVE, U.USER_PMPOPUP " .
            "FROM XW_USERS U, XW_FRIENDS F " .
            "WHERE F.FRIEND_ID=" . intval($user->getId()) . " " .
            "  AND U.USER_ID=F.USER_ID";
        $db = $this->db;
        $db->executeQuery($sql);
        $userList = new XWUserList();
        $tk = new XWScramblingToolKit();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $user = new XWUser();
            $user->setId($db->getResult($i, "USER_ID"));
            $user->setName($db->getResult($i, "USER_NAME"));
            $user->setEmail($tk->simpleDescrambling($db->getResult($i, "USER_EMAIL")));
            $user->setActive($db->getResult($i, "USER_ACTIVE") == 1);
            $userList->addUser($user);
        }
        return $userList;
    }

    //------ UserInfo ---------------

    /**
     * @param UserInterface $user
     *
     * @return XWUserInfo
     */
    public function loadUserInfoByUser($user)
    {
        $sql = "SELECT I.USERINFO_ID, " .
            "       I.USERINFO_ICQ, " .
            "       I.USERINFO_MSN, " .
            "       I.USERINFO_INFO, " .
            "       I.USERINFO_HOMEPAGE, " .
            "       I.USERINFO_SEX, " .
            "       I.USERINFO_SINGLE, " .
            "       I.USERINFO_LOCATION, " .
            "       I.USERINFO_AGE " .
            "FROM XW_USERINFOS I, XW_USERS_USERINFOS UI " .
            "WHERE UI.USER_ID=" . intval($user->getId()) . " " .
            "  AND I.USERINFO_ID=UI.USERINFO_ID";
        $db = $this->db;
        $db->executeQuery($sql);
        $info = new XWUserInfo();
        for ($i = 0; $i < $db->getCount(); $i++) {
            $info->setId($db->getResult($i, "USERINFO_ID"));
            $info->setIcq($db->getResult($i, "USERINFO_ICQ"));
            $info->setMsn($db->getResult($i, "USERINFO_MSN"));
            $info->setInfo($db->getResult($i, "USERINFO_INFO"));
            $info->setHomepage($db->getResult($i, "USERINFO_HOMEPAGE"));
            $info->setSex($db->getResult($i, "USERINFO_SEX"));
            $info->setSingle($db->getResult($i, "USERINFO_SINGLE"));
            $info->setLocation($db->getResult($i, "USERINFO_LOCATION"));
            $info->setAge($db->getResult($i, "USERINFO_AGE"));
            $info->setUserId($user->getId());
        }
        return $info;
    }

    /**
     * @param XWUserInfo $userInfo
     */
    public function saveUserInfo($userInfo)
    {
        if ($userInfo->getId() == 0) {
            $sql = "INSERT INTO XW_USERINFOS(" .
                "  USERINFO_ICQ," .
                "  USERINFO_MSN," .
                "  USERINFO_INFO," .
                "  USERINFO_SEX," .
                "  USERINFO_SINGLE," .
                "  USERINFO_LOCATION," .
                "  USERINFO_AGE," .
                "  USERINFO_HOMEPAGE" .
                ") VALUES (" .
                "  '" . $userInfo->getIcq() . "'," .
                "  '" . $userInfo->getMsn() . "'," .
                "  #{info}," .
                "  '" . $userInfo->getSex() . "'," .
                "  '" . $userInfo->getSingle() . "'," .
                "  '" . $userInfo->getLocation() . "'," .
                "  '" . $userInfo->getAge() . "'," .
                "  '" . $userInfo->getHomepage() . "'" .
                ") ";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("info", $userInfo->getInfo());

            $db = $this->db;
            $db->execute($stmt->getSQL());
            $sql = "SELECT USERINFO_ID " .
                "FROM XW_USERINFOS " .
                "WHERE USERINFO_ICQ='" . $userInfo->getIcq() . "' " .
                "  AND USERINFO_MSN='" . $userInfo->getMsn() . "' " .
                "  AND USERINFO_INFO=#{info} " .
                " ORDER BY USERINFO_ID ASC";

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("info", $userInfo->getInfo());

            $db->executeQuery($stmt->getSQL());
            for ($i = 0; $i < $db->getCount(); $i++) {
                $userInfo->setId($db->getResult($i, "USERINFO_ID"));
            }
            $sql = "INSERT INTO XW_USERS_USERINFOS (USERINFO_ID,USER_ID) " .
                "VALUES (" . intval($userInfo->getId()) . "," . intval($userInfo->getUserId()) . ")";
            $db->execute($sql);
        } else {
            $sql = "UPDATE XW_USERINFOS SET " .
                "  USERINFO_ICQ='" . $userInfo->getIcq() . "', " .
                "  USERINFO_MSN='" . $userInfo->getMsn() . "', " .
                "  USERINFO_INFO=#{info}, " .
                "  USERINFO_SEX='" . $userInfo->getSex() . "', " .
                "  USERINFO_SINGLE='" . $userInfo->getSingle() . "', " .
                "  USERINFO_LOCATION='" . $userInfo->getLocation() . "', " .
                "  USERINFO_AGE='" . $userInfo->getAge() . "', " .
                "  USERINFO_HOMEPAGE='" . $userInfo->getHomepage() . "' " .
                "WHERE USERINFO_ID=" . intval($userInfo->getId());

            $stmt = new XWSQLStatement($sql);
            $stmt->setString("info", $userInfo->getInfo());

            $db = $this->db;
            $db->execute($stmt->getSQL());
        }
    }
}
