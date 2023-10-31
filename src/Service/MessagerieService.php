<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MessagerieService{
    private ?string $login;
    private ?string $password;
    private ?string $server;
    private ?int $port;

    public function __construct(?string $login, ?string $password, ?string $server, ?int $port){
        $this->login = $login;
        $this->password = $password;
        $this->server = $server;
        $this->port = $port;
    }
    public function test(){
        return $this->port;
    }   
    public function sendMail(?string $object, ?string $content, ?string $destinataire){
        //Load Composer's autoloader
        require '../vendor/autoload.php';

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
            $mail->isSMTP();                                         
            $mail->Host       = $this->server;                    
            $mail->SMTPAuth   = true;              
            $mail->Username   = $this->login;    
            $mail->Password   = $this->password; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  
            $mail->Port       = $this->port; 

            //Recipients
            $mail->setFrom($this->login, 'Bot');
            $mail->addAddress($destinataire,); 

            //Content
            $mail->isHTML(true);
            $mail->Subject = $object;
            $mail->Body    = $content;
            $mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } 
}