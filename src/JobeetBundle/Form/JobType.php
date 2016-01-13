<?php

namespace JobeetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use JobeetBundle\Entity\Job;


class JobType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array('choices' => Job::getTypes(), 'expanded' => true))
            ->add('category')
            ->add('company')
            ->add('logo', null, array('label' => 'Company logo'))
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('howToApply', null, array('label' => 'How to apply?'))
            ->add('isPublic', null, array('label' => 'Public?'))
            ->add('email')
            ->add('file', FileType::class, array('label' => 'Company logo', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JobeetBundle\Entity\Job'
        ));
    }

    public function getName() {
        return 'job';
    }
}
