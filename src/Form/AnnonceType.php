<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add ('tableau_id',HiddenType::class,['required' => false])
            // on doit ajouter le type si on veux ajouter des propriete apres
            ->add('title',TextType::class,['required'=>TRUE] ) 
            // ->add('slug')
            ->add('price',MoneyType::class)
            ->add('introduction')
            ->add('content')
            ->add('coverImage',UrlType::class)
            ->add('rooms',IntegerType::class)
            ->add('images',CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add'=>true,
                'allow_delete'=>true,
            ])
            ->add ('file',FileType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'attr' => ['placeholder'=>'Choisir une images'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
