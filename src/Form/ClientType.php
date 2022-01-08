<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('adresse', TextType::class, [
                'attr' => ['role' => 'combobox', 'aria-autocomplete' => 'list', 'aria-expanded' => false, 'autocomplete' => 'off', 'aria-label' => 'Recherche'],
            ])
            ->add('mail')
            // ->add('operations')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
