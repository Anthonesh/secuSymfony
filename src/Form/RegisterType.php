<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', TextType::class,
            [
                'attr'=>['class'=>'nameRegister'],
                'label'=>'Saisir votre nom',
                'required'=>true
            ])
            ->add('firstName', TextType::class,
            [
                'attr'=>['class'=> 'firstNameRegister'],
                'label'=> 'Saisir le prénom',
                'required'=>true
            ])

            ->add('email', EmailType::class,
            [
                'attr'=>['class'=> 'emailRegister'],
                'label'=> 'Sasir votre email',
                'required'=>true
            ])
            ->add('password', RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'passwordRegister']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                
            ])
            ->add('submit', SubmitType::class,
            [
                'attr'=> ['class'=> 'submit'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
