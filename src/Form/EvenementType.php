<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Entity\TagEvenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre', 'attr' => ['class' => 'form-control']])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => ['rows' => 5, 'class' => 'form-control']])
            ->add('dateDebut', DateTimeType::class, ['label' => 'Date de début', 'widget' => 'single_text', 'attr' => ['class' => 'form-control']])
            ->add('dateFin', DateTimeType::class, ['label' => 'Date de fin', 'widget' => 'single_text', 'attr' => ['class' => 'form-control']])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un lieu',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('capaciteMax', NumberType::class, ['label' => 'Capacité max', 'attr' => ['class' => 'form-control']])
            ->add('prix', NumberType::class, ['label' => 'Prix (€)', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Conférence' => 'conference',
                    'Atelier' => 'atelier',
                    'Meetup' => 'meetup',
                    'Formation' => 'formation',
                    'Concert' => 'concert',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'class' => TagEvenement::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Evenement::class]);
    }
}
