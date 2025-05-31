<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRolesType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder->add('roles', ChoiceType::class, [
      'label' => 'Choisissez votre rôle',
      'choices' => [
        '🚗 Chauffeur' => 'ROLE_CHAUFFEUR',
        '👤 Passager' => 'ROLE_PASSAGER',
      ],
      'multiple' => true,
      'expanded' => true,
      'required' => true,
    ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => null, // car on va gérer manuellement les rôles dans le contrôleur
    ]);
  }
}
