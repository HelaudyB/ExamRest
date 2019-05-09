<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;



/**
 * Article controller.
 * @Route("/api", name="api_")
 */
class ArticleController extends AbstractFOSRestController
{

    /**
     * List all articles
     * @Rest\Get("/articles")
     *
     * @return Response
     */

    public function getAllArticle()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repository->findall();
        return $this->handleView($this->view($articles));
    }

    /**
     * Crée un article.
     * @Rest\Post("/article")
     *
     * @return Response
     */

    public function postArticle(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $data =json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }else{
            return $this->handleView($this->view($form->getErrors()));
        }

    }

    /**
     * Retourne un article
     * @Rest\Get("/articles/{idArticle}")
     */
    public function getArticle(int $idArticle)
    {
        $article = $repository= $this->getDoctrine()->getRepository((Article::class))->find($idArticle);
        if (is_null($article)) {
//            throw new  HttpException(404, "Article #".$article."n'existe pas");
            return $this->handleView($this->view(['message' => "Cet article n'existe pas"], Response::HTTP_NOT_FOUND));

        }
        return $article;

    }

    /**
     * Met a jour un article
     * @Rest\Put("/articles/{idArticle}")
     */
    public function putArticle(Request $request,int $idArticle)
    {
        $article =$repository = $this->getDoctrine()->getRepository(Article::class)->find($idArticle);
        if (is_null($article)) {
//            throw new HttpException(404, "Article".$idArticle."n'existe pas");
            return $this->handleView($this->view(['message' => "Cet article n'existe pas"], Response::HTTP_NOT_FOUND));

        }
        $data =json_decode($request->getContent(), true);
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($data);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $article = $form->getData();
            $em->persist($article);
            $em->flush();
            return $article;
        } else {
            return $form->getErrors();
        }
    }

    /**
     * Supprime un article
     * @Rest\Delete("/articles/{idArticle}")
     *
     */
    public function deleteArticle(int $idArticle)
    {
        $article = $repository = $this->getDoctrine()->getRepository(Article::class)->find($idArticle);
        if(is_null($article)){
//             throw new HttpException(404, "Article #".$idArticle.'n\'existe pas');
            return $this->handleView($this->view(['message' => "Cet article n'existe pas"], Response::HTTP_NOT_FOUND));

        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
            return JsonResponse::create(['success'=> true], 200);
        }
    }

}
