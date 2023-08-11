<?php

namespace App\Form;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Sensor;
use App\Repository\WidgetRepository;

class WidgetValuesType extends AbstractType
{
    protected int $widgetCount;

    public function __construct( WidgetRepository $widgetRepo ) {
        $this->widgetCount = count( $widgetRepo->findAll() );
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $positionValues = [];
        for( $i = 0; $i < $this->widgetCount + 1; $i++ ) {
            $positionValues[$i+1] = $i+1;
        }
        $builder
            ->add('name')
            ->add('position', ChoiceType::class, [
                'choices'  => $positionValues
            ])
            ->add('size', ChoiceType::class, [
                'choices'   => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ]
            ])
            ->add('sensors', EntityType::class, [
                'class' => Sensor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add( 'periodeUnit', HiddenType::class, [
                'data' => 'PM'
            ])
            ->add( 'periodeValue', HiddenType::class, [
                'data' => 10
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Widget::class,
        ]);
    }
}
