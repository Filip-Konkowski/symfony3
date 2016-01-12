<?php
/**
 * Created by PhpStorm.
 * User: filip
 * Date: 11.01.16
 * Time: 16:52
 */

namespace JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JobeetBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller
 * @package JobeetBundle\Controller
 *
 */

class CategoryController extends Controller
{
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('JobeetBundle:Category')->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $category->setActiveJobs($em->getRepository('JobeetBundle:Job')->getActiveJobs($category->getId()));

        $jobsQuery = $em->getRepository('JobeetBundle:Job')->getActiveJobsQuery($category->getId());

        $jobsPaginator = $this->get('knp_paginator');
        $pagination = $jobsPaginator->paginate($jobsQuery, $request->query->get('page', 1), 10);
        return $this->render('JobeetBundle:Category:show.html.twig', array(
            'category' => $category,
            'pagination' => $pagination,
        ));
    }
}