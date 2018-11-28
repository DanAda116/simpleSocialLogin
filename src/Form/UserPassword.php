<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 22.11.2018
 * Time: 14:34
 */

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, array(
                'label' => 'Your current password',
                'constraints' => array(
                    new \Symfony\Component\Security\Core\Validator\Constraints\UserPassword()
                )
            ))
            ->add('password_new', PasswordType::class, array(
                'label' => 'New password',
                'mapped' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save'
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