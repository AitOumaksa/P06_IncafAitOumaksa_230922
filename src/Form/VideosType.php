<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class VideosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('urlVideo' ,  UrlType::class,[
                'label' => 'Lien video',
                'default_protocol'=>'',
                'attr' => [
                'placeholder' => "Ajouter un lien",
                ],
                'constraints' => [
                   
                    new Assert\NotBlank([
                        'message' => 'Vous devez ajouter une video',
                    ]),
                    new Assert\Url(
                        message: 'Url de video doit étre , Exemple: "https://www.youtube.com/embed/y1S59PcUKb4"',
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
