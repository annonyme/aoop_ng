<?php
namespace core\mail;

use PHPMailer;

class SMTPMailer{
    private $host="";
    private $port=26;
    private $user="";
    private $password="";
     
    public function __construct(string $host, int $port=26, string $user="", string $password=""){
        $this->host=$host;
        $this->port=$port;
        $this->user=$user;
        $this->password=$password;
    }
    
    public function isValid(){
        return strlen($this->host) > 0 && strlen($this->user) > 0;
    }
     
    public function send(string $from, $fromClearName=null, array $to=[], string $subject, string $text = '', bool $isHtml = false, $fallbackText = null){
        $mail = new PHPMailer();
        //$text = preg_replace("/\\/", "", $text);
        $mail->IsSMTP();
        
        $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
                     
        $mail->SMTPAuth = true;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->Username = $this->user;
        $mail->Password = $this->password;
         
        $mail->SetFrom($from, $fromClearName);
        $mail->Subject = $subject;
         
        $mail->Body=$text;
        if($isHtml){
            $mail->isHTML(true);
            if($fallbackText){
                $mail->AltBody = $fallbackText;
            }
        }
         
        foreach($to as $name => $address){
            $mail->AddAddress($address, !preg_match("/^\d+$/", $name) ? $name : null);
        }
         
        $mail->Send();
    }
}