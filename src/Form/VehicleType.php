<?php

namespace App\Form;

use App\Entity\Vehicles;
use App\Entity\Brands;
use App\Entity\Energies;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('plate', TextType::class, [
        'label' => 'Plaque d’immatriculation'
      ])
      ->add('model', TextType::class, [
        'label' => 'Modèle du véhicule'
      ])
      ->add('color', TextType::class, [
        'label' => 'Couleur'
      ])
      ->add('registration', TextType::class, [
        'label' => 'Numéro d’immatriculation (ex: AB-123-CD)'
      ])
      ->add('date_first_registration', DateType::class, [
        'label' => 'Date de première immatriculation',
        'widget' => 'single_text',
      ])
      ->add('brand', TextType::class, [
        'label' => 'Marque du véhicule'
      ])
      ->add('energy', EntityType::class, [
        'class' => Energies::class,
        'choice_label' => function ($energy) {
          return sprintf(
            '%s — %d km • %d gCO₂/km',
            $energy->getName(),
            $energy->getAutonomyKm(),
            $energy->getCo2Emission()
          );
        },
        'label' => 'Énergie utilisée',
        'placeholder' => 'Choisissez une énergie',
      ])
      ->add('seats', IntegerType::class, [
        'label' => 'Nombre de places',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Vehicles::class,
    ]);
  }
}
