<?php

namespace App\Form;

use App\Entity\Preferences;
use App\Entity\UserPreferences;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferencesType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('smokerAllowed', CheckboxType::class, [
        'label' => 'Autoriser les fumeurs',
        'required' => false
      ])
      ->add('petsAllowed', CheckboxType::class, [
        'label' => 'Accepter les animaux',
        'required' => false
      ])
      ->add('musicPreference', TextType::class, [
        'label' => 'Musique ou silence',
        'required' => false
      ])
      ->add('talkingPreference', TextType::class, [
        'label' => 'Préférence discussions',
        'required' => false
      ])
      ->add('airConditioning', CheckboxType::class, [
        'label' => 'Climatisation activée',
        'required' => false
      ])
      ->add('latenessTolerance', IntegerType::class, [
        'label' => 'Tolérance aux retards (minutes)',
        'required' => false
      ])
      ->add('customPreferences', TextareaType::class, [
        'label' => 'Préférences personnelles',
        'required' => false
      ])
      ->add('preference', EntityType::class, [
        'class' => Preferences::class,
        'choice_label' => 'name',
        'label' => 'Préférence',
      ])
      ->add('value', TextType::class, [
        'label' => 'Valeur',
        'required' => false,
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => UserPreferences::class,
    ]);
  }
}
