<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiArticleController extends AbstractController
{

    private ArticleRepository $articleRepository;
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;

    public function __construct(ArticleRepository $articleRepository, SerializerInterface $serializer, EntityManagerInterface $em){
        $this->articleRepository = $articleRepository;
        $this->serializer = $serializer;
        $this->em = $em;
    }

    #[Route('/api/article', name: 'app_api_article')]
    public function index(): Response
    {
        return $this->render('api_article/index.html.twig', [
            'controller_name' => 'ApiArticleController',
        ]);
    }

    #[Route('/api/article/all', name:'app_api_article_all')]
    public function getAllArticle(): Response{
        $articles = $this->articleRepository->findAll();
        return $this->json($articles,200, ['Content-Type'=>'application/json', 'Access-Control-Allow-Origin'=>'*'], ['groups'=>'articles']);
    }
    
    #[Route('/api/article/id/{id}', name:'app_api_article_api')]
    public function getArticleById(int $id): Response{
        $article = $this->articleRepository->find($id);
        //test si l'article existe
        if($article){
            return $this->json($article,200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=>'*'], ['groups'=>'articles']);
        }
        //test si l'article n'existe pas
        else{
            return $this->json(['error : '=>'Aucun article'],206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=>'*']);
        }
    }

    #[Route('/api/article/add', name:'app_api_article_add', methods: ['POST'])]
public function addArticle(Request $request, UserRepository $userRepository): Response
{

    $json = $request->getContent();


    $articleData = $this->serializer->decode($json, 'json');

    // dd($articleData);
    
    $title = $articleData['title'];
    $content = $articleData['content'];
    $date = $articleData['date'];



    $article = new Article();

    $article->setTitle($title);
    $article->setContent($content);
    $article->setDate(new \DateTimeImmutable($articleData['date']));
    $article->setAuthor($userRepository->findOneBy(['email'=> UtilsService::cleanInput($articleData['author']['email'])]));

    $this->em->persist($article);

    $this->em->flush();

    return $this->json(['success' => 'Article ajouté avec succès'], 200);
}
}
