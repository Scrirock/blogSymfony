<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title')
            ->add('content', CKEditorType::class)
            ->add('cover', FileType::class, [
                'required' => false,
                'data_class' => null,
            ])
            ->add('isDraft', ChoiceType::class, [
                'choices' => [
                    'Brouillion' => 1,
                    'Version Finie' => 0
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
