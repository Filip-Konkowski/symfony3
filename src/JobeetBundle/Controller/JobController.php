<?php

namespace JobeetBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JobeetBundle\Entity\Job;
use JobeetBundle\Form\JobType;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{
    /**
     * Creates a form to delete a Job entity.
     *
     * @param Job $job The Job entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder(array('token' => $token))
            ->add('token', HiddenType::class)
            ->getForm();
    }

    private function createPublishForm($token){
        return $this->createFormBuilder(array('token' => $token))
                    ->add('token', HiddenType::class)
                    ->getForm();
    }

    /**
     * Lists all Job entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('JobeetBundle:Category')->getWithJobs();

        foreach ($categories as $category) {
            $category->setActiveJobs($em->getRepository('JobeetBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
            $category->setMoreJobs($em->getRepository('JobeetBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
        }

        return $this->render('JobeetBundle:job:index.html.twig', array(
            'categories' => $categories
        ));

    }

    /**
     * Creates a new Job entity.
     *
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $job->setType('full-time');
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($job);
            $em->flush();

            return $this->redirect($this->generateUrl('job_preview', array(
                'id' => $job->getId(),
                'location' => $job->getLocationSlug(),
                'company' => $job->getCompanySlug(),
                'position' => $job->getPositionSlug(),
                'token' => $job->getToken()
            )));
        }

        return $this->render('JobeetBundle:job:new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }


//    public function showAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('JobeetBundle:Job')->getActiveJob($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Job entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//
//        $token = $entity->getToken();
//        return $this->render('JobeetBundle:job:show.html.twig', array(
//            'token' => $token,
//            'entity' => $entity,
//            'delete_form' => $deleteForm->createView(),
//
//        ));
//    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
    public function editAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to fine Job entity');
        }
        $entity->setUpdatedAtValue();
        $editForm = $this->createForm('JobeetBundle\Form\JobType', $entity);
        $deleteForm = $this->createDeleteForm($token);
        $publishForm = $this->createPublishForm($token);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('job_edit', array('token' => $token)));
        }

        return $this->render('JobeetBundle:job:edit.html.twig', array(

            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
        ));

    }

    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('job_index'));

    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function previewAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

        if(!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity');
        }

        $deleteForm = $this->createDeleteForm($entity->getToken());
        $publishForm = $this->createPublishForm($entity->getToken());

        return $this->render('JobeetBundle:job:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView()
        ));
    }

    public function publishAction(Request $request, $token) {
        $form = $this->createPublishForm($token);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

        if($form->isValid()) {


            if(!$entity){
                throw $this->createNotFoundException('Unable to find Job Entity ');
            }
            $entity->publish();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your job is now online for 30 days');
        }
        return $this->redirectToRoute('job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        ));
    }

}
