<?php
namespace App\Form\rss\settings;

use App\Form\AbstractBaseFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RssEntryFlagType extends AbstractBaseFormType {

    public function buildForm(FormBuilderInterface $builder, array $options){
        $colors = $options['colors'];
        $builder
            ->add('color', ChoiceType::class, [
                'choices'=>$colors,
                'required' => true,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('name', TextType::class, ['required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => RssEntryFlagTypeData::class,
            'colors' => []
        ]);
    }

}
