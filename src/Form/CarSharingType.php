<?php

namespace App\Form;

use App\Entity\CarSharings;
use App\Entity\Vehicles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarSharingType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('startCity', TextType::class, [
        'label' => 'Ville de départ',
      ])
      ->add('endCity', TextType::class, [
        'label' => 'Ville d’arrivée',
      ])
      ->add('start_address', TextType::class, [
        'label' => 'Adresse de départ'
      ])
      ->add('end_address', TextType::class, [
        'label' => 'Adresse d’arrivée'
      ])
      ->add('date_start', DateTimeType::class, [
        'label' => 'Date/heure de départ',
        'widget' => 'single_text',
      ])
      ->add('date_end', DateTimeType::class, [
        'label' => 'Date/heure d’arrivée',
        'widget' => 'single_text',
      ])
      ->add('price', MoneyType::class, [
        'label' => 'Prix du trajet (€)',
        'currency' => 'EUR'
      ])
      ->add('total_places', IntegerType::class, [
        'label' => 'Nombre total de places'
      ])
      ->add('vehicle', EntityType::class, [
        'label' => 'Véhicule utilisé',
        'class' => Vehicles::class,
        'choice_label' => function ($vehicle) {
          return $vehicle->getBrand() . ' ' . $vehicle->getModel();
        },
        'placeholder' => 'Sélectionnez un véhicule',
        'required' => true
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => CarSharings::class,
    ]);
  }
}
