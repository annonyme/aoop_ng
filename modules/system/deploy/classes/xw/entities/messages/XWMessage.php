<?php
/*
 * Created on 19.06.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*
 * Copyright (c) 2007 Hannes Pries <http://www.annonyme.de>
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

namespace xw\entities\messages;

class XWMessage
{
    private $id = 0;
    private $content = "";
    private $viewed = false;
    private $deleted = false;
    //0.3.4 update
    private $title = "";
    private $userId = 0;
    private $receiverId = 0;
    private $date = "";
    private $header = "";

    //deprecated
    private $to = 0;
    private $from = 0;
    private $msgHeader = "";
    private $msgDate = "";


    public function __construct()
    {

    }

    public function save()
    {
        $dao = new XWMessageManagmentDAO();
        $dao->saveMessage($this);
    }

    public function delete()
    {
        $dao = new XWMessageManagmentDAO();
        $dao->deleteMessage($this);
    }

    public function load($id)
    {
        $dao = new XWMessageManagmentDAO();
        $msg = $dao->loadMessage($id);
        $this->id = $msg->getId();
        $this->content = $msg->getContent();
        $this->header = $msg->getHeader();
        $this->date = $msg->getDate();
        $this->viewed = $msg->isViewed();
        $this->deleted = $msg->isDeleted();

        $this->title = $msg->getTitle();
        $this->userId = $msg->getUserId();
        $this->receiverId = $msg->getReceiverId();

        //deprecated
        $this->to = $msg->getReceiverId();
        $this->from = $msg->getUserId();
    }

    public function wasViewed()
    {
        $dao = new XWMessageManagmentDAO();
        $dao->setViewedForMessage($this);
    }

    //---

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * deprecated
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * deprecated
     */
    public function setTo($to)
    {
        $this->to = $to;
        $this->receiverId = $to;
    }

    /**
     * deprecated
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * deprecated
     */
    public function setFrom($from)
    {
        $this->from = $from;
        $this->userId = $from;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isViewed()
    {
        return $this->viewed;
    }

    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
    }

    public function isDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        $this->from = $userId;
    }

    public function getReceiverId()
    {
        return $this->receiverId;
    }

    public function setReceiverId($receiverId)
    {
        $this->receiverId = $receiverId;
        $this->to = $receiverId;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setHeader($header)
    {
        $this->header = $header;
    }
}
