<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $ripo)
    {
        $articles = $ripo->findBy([],['id'=>'DESC']);

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/create", name="article_create")
     * @Route("/articles/{id}/edit", name="article_edit")
     */
    public  function form(Article $article = null, Request $request, EntityManagerInterface $em){

        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            $em->persist($article);
            $em->flush();

            dump($article);
            return $this->redirectToRoute("home");
        }

        return $this->render('articles/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/articles/{id}", name="article_show")
     */
    public function showArticle(Article $article,Request $request, EntityManagerInterface $em, UserInterface $user){

        $comment = new Comment();

        $form = $this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$comment->getId()){
                $comment->setCreatedAt(new \DateTime());
            }
            $comment->setArticle($article );
            $comment->setAuthor($user->getUsername());
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute("article_show", ['id' => $article->getId()]);
        }

        return $this->render('articles/article.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }


}
