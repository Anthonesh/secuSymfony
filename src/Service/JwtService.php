<?php
namespace App\Service;

use App\Repository\UserRepository;
use App\Service\UtilsService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class JwtService{
    private string $token;
    private UserRepository $userRepository;
    public function __construct(string $token, UserRepository $userRepository){
        $this->userRepository = $userRepository;
        $this->token = $token;
    }

    public function genToken($email){
        //construction du JWT
        require_once('../vendor/autoload.php');
        //Variables pour le token
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify('+60 minutes')->getTimestamp();
        $serverName = "your.domain.name";
        $userName   = $this->userRepository->findOneBy(['email'=>$email])->getName();
        $userFirstname   = $this->userRepository->findOneBy(['email'=>$email])->getFirstname();
        $id   = $this->userRepository->findOneBy(['email'=>$email])->getId();
        //Contenu du token
        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Timestamp génération du token
            'iss'  => $serverName,                       // Serveur
            'nbf'  => $issuedAt->getTimestamp(),         // Timestamp empécher date antérieure
            'exp'  => $expire,                           // Timestamp expiration du token
            'userName' => $userName,
            'userFirstName' => $userFirstname,
            'userId' => $id,                    
        ];
        //retourne le JWT token encode
        $token = JWT::encode(
            $data,
            $this->token,
            'HS512'
        );
        return $token;
    }
}