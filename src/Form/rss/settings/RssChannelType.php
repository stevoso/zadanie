<?php
namespace App\Form\rss\settings;

use App\Form\AbstractBaseFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RssChannelType extends AbstractBaseFormType {

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('subtitle', TextType::class, ['required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => RssChannelTypeData::class
        ]);
    }

}
