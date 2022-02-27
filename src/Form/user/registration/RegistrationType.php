<?php
namespace App\Form\user\registration;

use App\Form\AbstractBaseFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationType extends AbstractBaseFormType {

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('name', TextType::class, ['required' => false]);
        $builder->add('surname', TextType::class, ['required' => true]);
        $builder->add('login', TextType::class, ['required' => true]);
        $builder->add('password', TextType::class, ['required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => RegistrationTypeData::class
        ]);
    }

}
