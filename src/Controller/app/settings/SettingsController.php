<?php
namespace App\Controller\app\settings;

use App\Entity\rss\EntryFlag;
use App\Entity\user\User;
use App\Form\rss\settings\RssEntryFlagType;
use App\Form\rss\settings\RssEntryFlagTypeData;
use App\Service\entry\RssEntryManager;
use App\Service\ParametersApp;
use App\Service\settings\EntryFlagManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController {

    public function __construct(
        private RssEntryManager $rssEntryManager,
        private ParametersApp $parametersApp,
    ){ }

    #[Route('/settings', name: 'settings')]
    public function settings():Response{
        $isXmlFile = file_exists($this->parametersApp->pathRoot.'/public/rss.xml');
        return $this->render('webTemplates/settings/layout.html.twig', [
            'isXmlFile' => $isXmlFile
        ]);
    }

    #[Route('/update-xml', name: 'updateXml')]
    public function updateXml():RedirectResponse{
        $this->rssEntryManager->exportToXml();
        return $this->redirectToRoute('settings');
    }


}
