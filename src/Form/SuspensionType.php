<?php

namespace App\Form;

use App\Entity\Suspensions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuspensionType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('start_date', DateTimeType::class, [
        'label' => 'Début de suspension',
        'widget' => 'single_text',
      ])
      ->add('end_date', DateTimeType::class, [
        'label' => 'Fin de suspension',
        'widget' => 'single_text',
      ])
      ->add('reason', TextareaType::class, [
        'label' => 'Raison de la suspension',
        'attr' => ['rows' => 3],
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Suspensions::class,
    ]);
  }
}
