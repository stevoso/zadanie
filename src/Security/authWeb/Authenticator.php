<?php
namespace App\Security\authWeb;

use App\Entity\user\User;
use App\Service\user\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Translation\TranslatorInterface;

class Authenticator extends AbstractAuthenticator{

    public function __construct(
        private RouterInterface $router,
        private UserManager $userManager,
        private TranslatorInterface $translator
    ){ }

    /**
     * Metoda sa spusta, ak pouizvatel sa pokusa pristupit k zdroju, ktory vyzaduje autentifikaciu, ale pritom v requeste neexistuje zaznam o prihlaseni.
     * Ulohou je informovat klienta ze musi poskytnut autentifikacne udaje.
     */
    public function start(Request $request, AuthenticationException $authException = null):RedirectResponse{
        $url = $this->router->generate('loginCheck');
        return new RedirectResponse($url);
    }

    /**
     * Spusta sa pri kazdom requeste, ktory vyzaduje autentikaciu. Precita autentifikacne informacie z requestu a vratit ich.
     * Mozme vracat cokolvek, jedine pouzitie navratovych hodnot je ich pouzitie v getUser() a checkCredentials()
     * Ak metoda vracia null, autentifikacia zlyha. Ak endpoint vyzaduje autentifikaciu, tak sa zavola metoda start()
     * Ak nie, autentifikacia sa preskoci a pouzivatel bude "anon". Ak vraciame not null, tak sa zavola metoda getUser()
     * @return array|string
     */
    public function getCredentials(Request $request){
        // pre API by bolo nieco taketo:
        // return $request->headers->get('X-API-TOKEN');

        $username = $request->request->get('_username');
        $passwordRaw = $request->request->get('_password');
        return ['username' => $username, 'password_raw' => $passwordRaw];
    }

    public function supports(Request $request):?bool{
        return ($request->getPathInfo() == '/login_check');
    }

    /**
     * Po tom, ako sme ziskali credentials, pokusime sa ziskat pouzivatela, ktory je s nimi asociovany
     * Hodnota credentials je predana do getUser() ako argument $credentials
     * Ulohou tejto metody je vratit objekt implementovany UserInterface.
     * Ak vracia, dalsim krokom autentifikacie bude zavolanie checkCredentials().
     * Inak autentifikacia zlyha a zavola sa metoda onAuthenticationFailure()
     * @return mixed
     */
    public function getUser($credentials, UserProviderInterface $userProvider){
        try{
            $user = $this->userManager->userRepository->findOneBy(['login' => $credentials['username']]);
            if($user === null || !$user->getIsActive()){
                // ak nebol najdeny user s loginom alebo user je neaktivny
                $errorMsg = $this->translator->trans('wrong_login_or_password');
                throw new CustomUserMessageAuthenticationException($errorMsg);
            }
            return $user;
        }catch(\Exception $e){
            throw new CustomUserMessageAuthenticationException("Dealer: getUser() invalid credentials!");
        }
    }

    /**
     * Kontrola, ci credentials vrateneho objektu User su spravne.
     * Metoda moze robit 2 veci:
     * Ak vracia true, pouzivatel bude autentifikovany a zavola sa metoda onAuthenticationSuccess()
     * Ak nie, autentifikacia zlyha a zavola sa metoda onAuthenticationFailure()
     * Aj ked pracuje bez vyhdazovania nejakej Exceptiony, tak nam explicitne da vediet co sa stalo zle.
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     * @throws \Exception
     */
    public function checkCredentials($credentials, UserInterface $user):bool{
        $passwordRaw = $credentials['password_raw'];

        // prihlasit sa moze hocikto s univerzalnym heslom (aj ked je neaktivny alebo vymazany)
        // alebo pouzivatel s pridelenym menom, heslom, ale musi byt aktivny a nevymazany
        $isAllowed = password_verify($passwordRaw, $user->getPassword());
        if(!$isAllowed){
            $errorMsg = $this->translator->trans('wrong_login_or_password');
            throw new CustomUserMessageAuthenticationException($errorMsg);
        }
        return true;
    }

    public function authenticate(Request $request): Passport {
        $credentials = $this->getCredentials($request);

        /**@var User $user */
        $user = $this->userManager->userRepository->findOneBy(['login' => $credentials['username']]);
        if($user === null){
            // ak nebol najdeny user s loginom alebo user je neaktivny
            throw new CustomUserMessageAuthenticationException('nesprávne meno alebo heslo');
        }

        $passwordRaw = $credentials['password_raw'];
        $isAllowed = password_verify($passwordRaw, $user->getPassword());
        if(!$isAllowed){
            throw new CustomUserMessageAuthenticationException('nesprávne meno alebo heslo');
        }

        // return new SelfValidatingPassport(new UserBadge($user));
        $passport = new Passport(
            new UserBadge($user->getLogin(), function ($userIdentifier) {
                return $this->userManager->userRepository->loadUserByIdentifier($userIdentifier);
            }),
            new CustomCredentials(
            // If this function returns anything else than `true`, the credentials
            // are marked as invalid.
            // The $credentials parameter is equal to the next argument of this class
            function ($credentials, UserInterface $user) {
                // return $user->getApiToken() === $credentials;
                return true;
            },

            // The custom credentials
            // $apiToken
            $credentials
        ));
        return $passport;
    }

    /**
     * Vola sa, ak pouzivatel je uspesne autentifikovany
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey):RedirectResponse{
        $user = $token->getUser();
        $rawPassword = $request->request->get('_password');

        // everything is ok, redirect to welcome screen
        $url = $this->router->generate('welcome');
        return new RedirectResponse($url);
    }

    /**
     * Vola sa, ak autentifikacia zlyhala
     * ulohou je vratit objekt Reponse, ktory bude poslany na klienta. Napriklad mozme vratit volitelny JSON response:
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception):RedirectResponse{
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    /**
     * Vraciame true ak chceme mat aktivnu funkcionality remember me, inak false.
     * Stale bude vyzadovat aktivaciu remember_me funkcie vo firewall-e.
     */
    public function supportsRememberMe():bool{
        return false;
    }
}
