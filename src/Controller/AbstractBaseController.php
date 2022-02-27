<?php
namespace App\Controller;

use App\Model\authAndHttp\RequestErrors;
use App\Service\ParametersApp;
use Sentia\Utils\SentiaUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractBaseController extends AbstractController {

    protected function __construct(
        public SentiaUtils $utils, // musi byt public, lebo sa volaju napr. aj v TokenSubscriber
        public ParametersApp $parametersApp
    ){ }

}
