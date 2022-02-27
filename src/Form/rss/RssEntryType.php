<?php
namespace App\Form\rss;

use App\Form\AbstractBaseFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RssEntryType extends AbstractBaseFormType {

    public function buildForm(FormBuilderInterface $builder, array $options){
        $idRssChannels = $options['idRssChannels'];
        $builder
            ->add('idRssChannel', ChoiceType::class, [
                'choices'=>$idRssChannels,
                'required' => true,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('title', TextType::class, ['required' => true])
            ->add('link', TextType::class, ['required' => true])
            ->add('summary', TextareaType::class, ['required' => false])
            ->add('content', TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => RssEntryTypeData::class,
            'idRssChannels' => []
        ]);
    }

}
