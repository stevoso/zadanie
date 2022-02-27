<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

abstract class AbstractBaseFormType extends AbstractType {

    public function getName(): string {
        $s = get_class($this);
        $i = strrpos($s, '\\');
        if($i !== false){
            $s = substr($s, $i + 1);
        }
        return $s;
    }
}
