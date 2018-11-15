<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 14.11.2018
 * Time: 14:59
 */

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserAvatar extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('avatarImage', FileType::class, array(
               'label'=> false,
               'data_class' => null,
               'attr' => array(
                   'class'=> 'custom-file-input',
                   'id' => 'customFile'
               )
           ))
           ->add('save', SubmitType::class, array(
               'label' => 'Save',
               'attr' => array(
                   'class' => 'btn btn-secondary',
                   'type' => 'button'
                )
           ))
           ->getForm();
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }
}