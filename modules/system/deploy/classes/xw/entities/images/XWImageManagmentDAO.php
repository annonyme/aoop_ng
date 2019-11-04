<?php
/*
 * Created on 11.07.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /*
  * Copyright (c) 2007/2010/2014 Hannes Pries <http://www.annonyme.de>
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

namespace xw\entities\images;

use core\utils\XWServerInstanceToolKit;
use PDBC\PDBCCache;
use core\database\XWSQLStatement;
 
class XWImageManagmentDAO{
	private $db=null;
	
	static private $instance=null;
	 
	static public function instance(){
		if(self::$instance==null){
			self::$instance=new XWImageManagmentDAO();
		}
		return self::$instance;
	}
	
	public function __construct(){
		$dbName=XWServerInstanceToolKit::instance()->getServerSwitch()->getDbname();
        $this->db=PDBCCache::getInstance()->getDB($dbName);
	}
	
	public function loadImage($id){
        $sql="SELECT P.PHOTO_ID, " .
        	 "       P.PHOTO_PATH, " .
        	 "       P.PHOTO_DATE, " .
        	 "       P.PHOTO_FRIENDS, " .
        	 "       P.PHOTO_DRAFT, " .
        	 "       P.PHOTO_TITLE, " .
        	 "       UP.USER_ID " .
        	 "FROM XW_PHOTOS P, XW_USERS_PHOTOS UP " .
        	 "WHERE P.PHOTO_ID=".intval($id)." " .
        	 "  AND UP.PHOTO_ID=P.PHOTO_ID";             
        $db=$this->db;
        $db->executeQuery($sql);
        $image=new XWImage();
        for($i=0;$i<$db->getCount();$i++){
            $image->setId($db->getResult($i,"PHOTO_ID"));
            $image->setPath($db->getResult($i,"PHOTO_PATH"));
            $image->setUserId($db->getResult($i,"USER_ID"));            
            $image->setFriends($db->getResult($i,"PHOTO_FRIENDS")==1);
            $image->setDraft($db->getResult($i,"PHOTO_DRAFT")==1);
            $image->setDate($db->getResult($i,"PHOTO_DATE"));
            $image->setTitle($db->getResult($i,"PHOTO_TITLE"));
        }
        return $image;
	}
	
	public function saveImage($image){
		$db=$this->db;
		
		$friends=0;
        if($image->isFriends()){
            $friends=1;
        }
        $draft=0;
        if($image->isDraft()){
        	$draft=1;
        }
            
		if($image->getId()==0){            
            $sql="INSERT INTO XW_PHOTOS(" .
            	 "	PHOTO_PATH," .
            	 "	PHOTO_FRIENDS," .
            	 "	PHOTO_DATE," .
            	 "	PHOTO_DRAFT," .
            	 "	PHOTO_TITLE) " .
            	 "VALUES (" .
            	 "	#{path}, " .
            	 "	".$friends.", " .
            	 "	CURRENT_TIMESTAMP, " .
            	 "	".$draft.", " .
            	 "	#{title})";
            	 
            $stmt=new XWSQLStatement($sql);
            $stmt->setString("title",$image->getTitle());	
            $stmt->setString("path",$image->getPath());	 
            	 
            $db->execute($stmt->getSQL());
            
            $sql="SELECT PHOTO_ID FROM XW_PHOTOS WHERE PHOTO_PATH='".$image->getPath()."'";            
            $db->executeQuery($sql);
            $image->setId($db->getResult(0,"PHOTO_ID"));
            $sql="INSERT INTO XW_USERS_PHOTOS(USER_ID,PHOTO_ID) VALUES (".intval($image->getUserId()).",".intval($image->getId()).")";
            $db->execute($sql);
        }
        else{
            $sql="UPDATE XW_PHOTOS SET " .
            	 "  PHOTO_PATH=#{path}, " .
            	 "  PHOTO_FRIENDS=".$friends."," .
            	 "  PHOTO_DRAFT=".$draft.", " .
            	 "  PHOTO_TITLE=#{title}, ".
            	 "  PHOTO_DATE='".$image->getDate()."' ".
                 " WHERE PHOTO_ID=#{id}";            
            
            $stmt=new XWSQLStatement($sql);
            $stmt->setString("title",$image->getTitle());
            $stmt->setInt("id",$image->getId());
            $stmt->setString("path",$image->getPath());
            
            $db->execute($stmt->getSQL());
        }
        return $image;
	}
	
	public function deleteImage($image){
		$db=$this->db;
		$sql="DELETE FROM XW_PHOTOS WHERE PHOTO_ID=".intval($image->getId());
        $db->execute($sql);
        $sql="DELETE FROM XW_USERS_PHOTOS WHERE PHOTO_ID=".intval($image->getId());
        $db->execute($sql);
	}
	
	public function loadListByUser($user){
		$db=$this->db;
             
        $sql="SELECT P.PHOTO_ID, " .
        	 "       P.PHOTO_PATH, P.PHOTO_DATE, P.PHOTO_FRIENDS, P.PHOTO_DRAFT, P.PHOTO_TITLE, UP.USER_ID " .
        	 "FROM XW_PHOTOS P, XW_USERS_PHOTOS UP " .
        	 "WHERE UP.USER_ID=".intval($user->getId())." " .
        	 "  AND P.PHOTO_ID=UP.PHOTO_ID " .
        	 "ORDER BY P.PHOTO_DATE DESC";  

        $image=null;
        $list=new XWImageList();
        $db->executeQuery($sql);
        for($i=0;$i<$db->getCount();$i++){
            $image=new XWImage();
            $image->setId($db->getResult($i,"PHOTO_ID"));
            $image->setPath($db->getResult($i,"PHOTO_PATH"));
            $image->setUserId($db->getResult($i,"USER_ID"));
            $image->setFriends($db->getResult($i,"PHOTO_FRIENDS")==1);
            $image->setDate($db->getResult($i,"PHOTO_DATE"));
            $image->setDraft($db->getResult($i,"PHOTO_DRAFT")==1);
            $image->setTitle($db->getResult($i,"PHOTO_TITLE"));
            $list->addImage($image);
        }
        return $list;
	}
	
	public function loadListByLatest($limit="10"){
		$db=$this->db;
             
        $sql="SELECT P.PHOTO_ID, P.PHOTO_PATH, P.PHOTO_DATE, P.PHOTO_FRIENDS, P.PHOTO_DRAFT, P.PHOTO_TITLE, UP.USER_ID " .
        	 "FROM XW_PHOTOS P, XW_USERS_PHOTOS UP " .
        	 "WHERE UP.PHOTO_ID=P.PHOTO_ID " .
        	 "ORDER BY P.PHOTO_DATE DESC " .
        	 "LIMIT 0,".intval($limit);  

        $image=null;
        $list=new XWImageList();
        $db->executeQuery($sql);
        for($i=0;$i<$db->getCount();$i++){
            $image=new XWImage();
            $image->setId($db->getResult($i,"PHOTO_ID"));
            $image->setPath($db->getResult($i,"PHOTO_PATH"));
            $image->setUserId($db->getResult($i,"USER_ID"));
            $image->setFriends($db->getResult($i,"PHOTO_FRIENDS")==1);
            $image->setDate($db->getResult($i,"PHOTO_DATE"));
            $image->setDraft($db->getResult($i,"PHOTO_DRAFT")==1);
            $image->setTitle($db->getResult($i,"PHOTO_TITLE"));
            $list->addImage($image);
        }
        return $list;
	}
}
