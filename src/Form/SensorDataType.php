<?php

namespace App\Form;

use App\Entity\SensorData;
use App\Entity\Sensor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SensorDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('date')
            ->add('sensor', EntityType::class, [
                'class'         => Sensor::class,
                'choice_label'  => 'name',
                'disabled'      => true
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SensorData::class,
        ]);
    }
}
