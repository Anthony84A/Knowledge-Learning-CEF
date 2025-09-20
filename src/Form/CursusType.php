<?php

namespace App\Form;

use App\Entity\Cursus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CursusType Form
 *
 * Symfony form used to create or edit a Cursus entity.
 *
 * Fields:
 * - title: Text field for the course title
 * - description: Textarea for the course description
 * - price: Money field for the course price in EUR
 */
class CursusType extends AbstractType
{
    /**
     * Build the form fields.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Course Title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price (€)',
                'currency' => 'EUR'
            ]);
    }

    /**
     * Configure the options for this form.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cursus::class,
        ]);
    }
}
