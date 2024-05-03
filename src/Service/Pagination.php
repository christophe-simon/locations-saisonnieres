<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $route;
    private $manager;
    private $twig;
    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPAge($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setEntityClass de votre objet Pagination");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;

        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        return $data;
    }

    public function getPagesNumber()
    {
        if (empty($this->entityClass)) {
            throw new Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setEntityClass de votre objet Pagination");
        }

        $repo = $this->manager->getRepository($this->entityClass);
        $totalItemsNumber = count($repo->findAll());

        $pagesNumber = ceil($totalItemsNumber / $this->limit);

        return $pagesNumber;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'currentPage' => $this->getCurrentPage(),
            'pagesNumber' => $this->getPagesNumber(),
            'route' => $this->route
        ]);
    }
}
