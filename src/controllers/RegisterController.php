<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\models\Message;
use wishlist\tools;
use wishlist\vues\VueRegister;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class RegisterController{
    const LOGIN = 'login';
    const SIGNUP = 'signUp';
    const TOKEN = 'token';
    const MONCOMPTE = 'monCompte';
    const CHANGEMAIL = 'changeMail';
    const CHANGEPSW = 'changePsw';
    const DELETE_ACCOUNT = 'deleteAcc';
    const TAILLE_USERNAME_MIN = 4;
    const TAILLE_USERNAME_MAX = 100;
    const TAILLE_MDP_MIN = 8;
    const TAILLE_MDP_MAX = 256;

    /**
     * @var object container
     */
    private object $c;

    /**
     * Constructeur de RegisterController
     * @param object $c container
     */
    public function __construct(object $c) {
        $this->c = $c;
    }

    /**
     * Traitement de l'inscription d'un utilisateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function newUser(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('signupConfirm');
        $url = $base . $route_uri;
        $content = $rq->getParsedBody();

        $NomUtilisateur = filter_var($content['username'], FILTER_SANITIZE_STRING);
        $MotDePasse = $content['password'];
        $options = ['cost' => 12];
        $MotDePasseConfirm = $content['password_confirm'];
        $Email = filter_var($content['email'], FILTER_SANITIZE_EMAIL);

        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if (strlen($NomUtilisateur) < self::TAILLE_USERNAME_MIN) {
            $notifMsg = urlencode("Ce nom d'utilisateur est trop court. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($NomUtilisateur) > self::TAILLE_USERNAME_MAX) {
            $notifMsg = urlencode("Ce nom d'utilisateur est trop long. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ($userNameExist != 0) {
            $notifMsg = urlencode("Ce nom d'utilisateur est déjà pris. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) < self::TAILLE_MDP_MIN) {
            $notifMsg = urlencode("Ce mot de passe est trop court. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) > self::TAILLE_MDP_MAX) {
            $notifMsg = urlencode("Ce mot de passe est trop long. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ($MotDePasseConfirm != $MotDePasse) {
            $notifMsg = urlencode("Les mots de passe ne correspondent pas. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else {
            $MotDePasse = password_hash($MotDePasse, PASSWORD_DEFAULT, $options);
            $newUser = new Authenticate();
            $newUser->username=$NomUtilisateur;
            $newUser->password=$MotDePasse;
            $newUser->email=$Email;
            $newUser->Niveau_acces=1;
            $newUser->save();

            // gestion session
            $this->sessionConnexion($newUser);

            $notifMsg = urlencode("Vous êtes connecté en tant que $NomUtilisateur.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        }
    }

    /**
     * Traitement de la connexion d'un utilisateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function authentification(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('loginConfirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];


        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if ($userNameExist == 1) {
            $GetUser=Authenticate::where("username","=",$NomUtilisateur)->first();
            $HashedPassword=$GetUser->password;
            if (password_verify($MotDePasse,$HashedPassword)) {
                $user = Authenticate::where('username', '=', $NomUtilisateur)->first();

                $this->sessionConnexion($user);

                $notifMsg = urlencode("Vous êtes connecté en tant que $NomUtilisateur.");
                return $rs->withRedirect($base."?notif=$notifMsg");
            }
        }

        $this->sessionDeconnexion();

        $notifMsg = urlencode("Mot de passe ou nom d'utilisateur incorrect.");
        return $rs->withRedirect($base."/login?notif=$notifMsg");
    }

    /**
     * Affichage de la page permettant la connexion d'un utilisateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function loginPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('login');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::LOGIN, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de la page permettant l'inscription d'un utilisateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function signUpPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('signUp');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::SIGNUP, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de la page permettant de trouver une liste à partir d'un token
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function accessListToken(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('token');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::TOKEN, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Traitement de la déconnexion d'un utilisateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function logout(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('logout');
        $url = $base . $route_uri;


        $this->sessionDeconnexion();
        
        $notifMsg = urlencode("Vous avez été déconnecté.");
        
        
        return $rs->withRedirect($base."/login?notif=$notifMsg");
    }

    /**
     * Affichage du compte de l'utilisateur connecté
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function monComptePage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('monCompte');
        $url = $base . $route_uri;

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([$user], RegisterController::MONCOMPTE, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de la page permettant à un utilisateur connecté de changer d'adresse email
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function changeMailPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changeMailPage');
        $url = $base . $route_uri;

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([$user], RegisterController::CHANGEMAIL, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Traitement du changement d'adresse email d'un utilisateur connecté
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function changeMail(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changeMailConfirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $email1 = filter_var($content['email'], FILTER_SANITIZE_EMAIL);
        $email2 = filter_var($content['email_confirm'], FILTER_SANITIZE_EMAIL);

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        } else if ($email1 != $email2) {
            $notifMsg = urlencode("Les adresses email ne correspondent pas. Réessayez.");
            return $rs->withRedirect($base . "/changeMail?notif=$notifMsg");
        }

        Authenticate::where("username", "=", $_SESSION['username'])->update(['email' => $email1]);

        $notifMsg = urlencode("Votre adresse mail a bien été modifiée en \"$email1\".");
        return $rs->withRedirect($base . "/monCompte?notif=$notifMsg");
    }

    /**
     * Affichage de la page permettant à un utilisateur connecté de changer de mot de passe
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function changePswPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changePassword');
        $url = $base . $route_uri;

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([$user], RegisterController::CHANGEPSW, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Traitement du changement de mot de passe d'un utilisateur connecté
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function changePsw(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changePasswordConfirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $oldpass = $content['oldpass'];
        $newPass1 = $content['newpass1'];
        $newPass2 = $content['newpass2'];

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

        if (strlen($newPass1) < self::TAILLE_MDP_MIN) {
            $notifMsg = urlencode("Ce mot de passe est trop court. Réessayez.");
            return $rs->withRedirect($base."/changePassword?notif=$notifMsg");
        } else if (strlen($newPass1) > self::TAILLE_MDP_MAX) {
            $notifMsg = urlencode("Ce mot de passe est trop long. Réessayez.");
            return $rs->withRedirect($base."/changePassword?notif=$notifMsg");
        } else if ($newPass1 != $newPass2) {
            $notifMsg = urlencode("Les mots de passe ne correspondent pas. Réessayez.");
            return $rs->withRedirect($base."/changePassword?notif=$notifMsg");
        } else if (!password_verify($oldpass, $user->password)) {
            $notifMsg = urlencode("Mot de passe incorrect.");
            return $rs->withRedirect($base."/changePassword?notif=$notifMsg");
        } else {
            $options = ['cost' => 12];
            $newHash = password_hash($newPass2, PASSWORD_DEFAULT, $options);

            Authenticate::where("id", "=", $user->id)->update(['password' => $newHash]);

            $this->sessionDeconnexion();

            $notifMsg = $notifMsg = urlencode("Votre mot de passe a été modifié. Reconnectez-vous.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }
    }

    /**
     * Affichage de la page permettant à un utilisateur connecté de supprimer son compte
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function deleteAccountPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('deleteAccountPage');
        $url = $base . $route_uri;

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([$user], RegisterController::DELETE_ACCOUNT, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Traitement de la suppression de compte d'un utilisateur connecté
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function deleteAccount(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('delAccConfirm');
        $url = $base . $route_uri;

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $content = $rq->getParsedBody();

        $confirmation = (isset($content['confirm']) && filter_var($content['confirm'], FILTER_SANITIZE_NUMBER_INT) == 1 ? 1 : 0);
        if ($confirmation != 1) {
            $notifMsg = urlencode("Cochez la case pour supprimer votre compte.");
            return $rs->withRedirect($base."/deleteAccount?notif=$notifMsg");
        } else {
            $user = Authenticate::where("username", "=", $_SESSION['username'])->first();

            $lists = Liste::where("user_id", "=", $user->id)->get();
            foreach ($lists as $list) {
                $items = $list->items;
                foreach ($items as $item) {
                    $image = $item->img;
                    is_null($image) || $image == "" ? : unlink("$base/img/$image");
                    $item->delete();
                }
                $messages = $list->messages;
                foreach ($messages as $message) {
                    $message->delete();
                }
                $list->delete();
            }

            $mesMessages = Message::where("id_user", "=", $user->id)->get();
            foreach ($mesMessages as $monMessage) {
                $monMessage->delete();
            }

            $mesReservations = Item::where("reserv_par", "=", $user->id)->get();
            foreach ($mesReservations as $maReservation) {
                $l = $maReservation->liste;
                if ((strtotime($l->expiration) < strtotime(date("Y-m-d")))) {
                    // liste expiree
                    $maReservation->update(["pseudo" => $user->username]);
                } else {
                    // liste toujours en cours
                    $maReservation->update(["etat_reserv" => 0]);
                    $maReservation->update(["msg_reserv" => ""]);
                }
                $maReservation->update(["reserv_par" => 0]);
            }

            $user->delete();

            $this->sessionDeconnexion();

            $notifMsg = urlencode("Votre compte a bien été supprimé.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        }
    }

    /**
     * Gestion de la session lors de la connexion d'un utilisateur
     * @param Authenticate $user Utilisateur connecté
     * @return void
     */
    private function sessionConnexion(Authenticate $user): void {
        if (isset($_SESSION['username'])) {
            session_destroy();
            session_start();
        }
        $_SESSION['username'] = $user['username'];
        $_SESSION['AccessRights'] = $user['Niveau_acces'];
    }

    /**
     * Gestion de la session lors de la déconnexion d'un utilisateur
     * @return void
     */
    private function sessionDeconnexion(): void {
        session_destroy();
        session_start();
    }
}



