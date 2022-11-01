<?php

namespace App\Form;


use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
 

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',  TextType::class , ['label' => 'Nom de la figure :' ,'attr' => [
                'placeholder' => "Ajouter nom de la figure"
            ]])
            ->add('description', TextareaType::class , ['label' => 'Description de la figure : ' ,'attr' => [
                'placeholder' => "Ajouter description de la figure"
            ]])
            ->add('category' , TextType::class , ['label' => 'Groupe de la figure : ' ,'attr' => [
                'placeholder' => "Ajouter groupe de la figure"
            ]] )
            ->add('images', CollectionType::class, [
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true ,
                'label'=> false,
                'by_reference' => false,
                'attr' => ['class' => 'border  border-info border-2 rounded'],
                'prototype_options'  => [
                    'label'=> false,
                    'attr' => ['class' => 'collection'],
                ],
                'entry_options'  => [
                    'label'=> false,
                    'attr' => ['class' => 'collection'],
                    
                ],
                
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideosType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label'=> false,
                'by_reference' => false,
                'attr' => ['class' => 'border border-info border-2 rounded'],
                'prototype_options'  => [
                    'label'=> false,
                    'attr' => ['class' => 'collection'],
                ],
                'entry_options'  => [
                    'label'=> false,
                    'attr' => ['class' => 'collection'],
                ],
            
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
