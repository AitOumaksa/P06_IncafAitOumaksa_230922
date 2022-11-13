<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userName' , TextType::class, [
                'label' => "Nom d'utilisateur",
                'attr' => [
                    'placeholder' => "Votre Nom d'utilisateur"
                ],
            ])
            ->add('email' , EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'placeholder' => "Votre adresse E-mail"
                ],
            ])
            ->add('avatar' ,  FileType::class, ['label' => "Photo d'utilisateur",
            'mapped' => false,
            'attr' => ['class' => 'd-none'],
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Veuillez insérer une image en .jpg, .jpeg ou .png !',
                ]),
                new NotBlank([
                    'message' => 'Vous devez ajouter une photo ',
                ])
            ],
           ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => "Votre mot de passe"
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrez un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})+$/',
                        'message' => 'Mot de passe doit contenir 8 caractères dont au minimum une majuscule, une minuscule, un caractère numérique et un caractère spécial'
                    ]),
                ],
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
