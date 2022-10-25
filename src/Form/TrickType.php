<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',  TextType::class , ['label' => 'Nom de la figure :'])
            ->add('description', TextareaType::class , ['label' => 'Description de la figure : '])
            ->add('category' , TextType::class , ['label' => 'Groupe de la figure : '] )
            ->add('slug')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('user')
            ->add('images',  FileType::class, [
                'label' => 'Images de la figure',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                
               
                 
            ])
            ->add('Ajouter', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
