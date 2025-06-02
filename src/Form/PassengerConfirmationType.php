<?php

namespace App\Form;

use App\Entity\PassengerConfirmation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PassengerConfirmationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('confirmed', ChoiceType::class, [
        'label' => 'Avez-vous bien effectué ce trajet ?',
        'choices' => [
          '✅ Oui, tout s’est bien passé' => true,
          '❌ Non, j’ai eu un problème' => false,
        ],
        'expanded' => true,
        'multiple' => false,
      ])
      ->add('comment', TextareaType::class, [
        'label' => 'Commentaire (obligatoire en cas de problème)',
        'required' => false,
        'attr' => ['placeholder' => 'Expliquez le souci rencontré...']
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => PassengerConfirmation::class,
    ]);
  }
}
