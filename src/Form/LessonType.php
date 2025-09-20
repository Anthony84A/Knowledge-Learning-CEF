<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Cursus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LessonType Form
 *
 * Symfony form used to create or edit a Lesson entity.
 *
 * Fields:
 * - title: Text field for the lesson title
 * - description: Textarea for the lesson description
 * - price: Money field for the lesson price in EUR
 * - cursus: EntityType to select the associated Cursus
 */
class LessonType extends AbstractType
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
                'label' => 'Lesson Title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price (€)',
                'currency' => 'EUR'
            ])
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'title',
                'label' => 'Associated Course'
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
            'data_class' => Lesson::class,
        ]);
    }
}
