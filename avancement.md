# Légende
* ✅ : Fonctionnalité réalisée.
* ✅ 🟨 : Fonctionnalité réalisée, mais fonctionne different de ce qui a été demandé. Une précision est présente en dessous.
* ⛔ : On a décidé de ne pas travailler sur cette fonctionnalité.

# Participant
## 1 Afficher une liste de souhaits
### Tâches
* ✅ L'affichage du détail d'une liste présente toutes les informations de la liste accompagnées de la liste des items
* ✅ Chaque item est affiché avec son nom, son image et l'état de la réservation
* ✅ L'affichage de l'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant et les messages n'apparaissent pas avant la date d'échéance
* ✅ un clic sur un item donne accès à son détail
* ✅ Pour afficher une liste, il faut connaître son URL contenant un token
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 2 Afficher un item d'une liste
### Tâches
* ✅ L'affichage d'un item présente toutes ses informations détaillées, son image et l'état de la réservation (nom du participant sans message)
* ✅ L'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant n’apparaît pas
* ✅ Un item appartenant à aucune liste validée (par son créateur) ne peut pas être affiché
* ✅ Pour afficher un item d'une liste, il faut connaître l'URL de sa liste contenant un token
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 3 Réserver un item
### Tâches
* ✅ Dans la page de l'item, si l'item n'est pas réservé, un formulaire permet de saisir le nom du participant
* ✅ La validation du formulaire enregistre la participation
* ✅ 🟨 ~~Le nom du participant peut être mémorisé dans une variable de session ou un cookie pour préremplir le champ afin de ne pas avoir à le retaper~~
  * _On ne met pas de champ pour le pseudo si l'utilisateur est connecté._
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 4 Ajouter un message avec sa réservation
### Tâches
* ✅ Dans la page de l'item, si l'item n'est pas réservé, le formulaire de participation permet également de saisir un message destiné le créateur
* ✅ La validation du formulaire enregistre le message avec la participation
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 5 Ajouter un message sur une liste
### Tâches
* ✅ Dans la page d'une liste, un formulaire permet d'ajouter un message public rattaché à la liste
* ✅ Les messages sur la liste seront affichés avec le détail de la liste
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

# Créateur
## 6 Créer une liste
### Tâches
* ✅ Un utilisateur non authentifié peut créer une nouvelle liste de souhaits
* ✅ Un formulaire lui permet de saisir les informations générales de la liste
* ✅ les informations sont : titre, description et date d'expiration
* ✅ Les balises HTML sont interdites dans ces champs
* ✅ Lors de sa création un token est créé pour accéder à cette liste en modification
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 7 Modifier les informations générales d'une de ses listes
### Tâches
* ✅ Le créateur d'une liste peut modifier les informations générales de ses listes
* ✅ Pour modifier il doit connaître son URL de modification (avec token)
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 8 Ajouter des items
### Tâches
* ✅ Le créateur d'une liste peut ajouter des items à une de ses listes après l'avoir sélectionnée par son URL de modification (avec token)
* ✅ 🟨 Un formulaire permet de saisir les informations de l'item
* ✅ 🟨 les informations sont : nom et description et prix
* ✅ 🟨 il peut aussi fournir l'URL d'une page externe qui détaille le produit (sur un site d'e-commerce par exemple)
  * _Finalement, on crée un item par défaut, et si on veut ajouter des informations, on peut le modifier._
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 9 Modifier un item
### Tâches
* ✅ Le créateur d'une liste peut modifier les informations des items de ses listes
* ✅ Une fois réservé, un item ne peut plus être modifié
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 10 Supprimer un item
### Tâches
* ✅ Le créateur d'une liste peut supprimer un item d'un de ses listes s'il n'est pas réservé
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 11 Rajouter une image à un item
### Tâches
* ✅ Le créateur d'une liste peut ajouter une image à un de ses items
* ✅ 🟨 ~~Pour cela il fournit l'URL complète d'une image externe (acceptant le hot-linking) ou bien le chemin relatif d'une image déjà présente dans le dossier web/img/~~
  * _On peut uploader des images, donc on utilise cette méthode._
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 12 Modifier une image d'un item
### Tâches
* ✅ 🟨 ~~Le créateur d'une liste peut modifier l'URL de l'image de ses items~~
  * _Le créateur d'une liste peut modifier l'image en faisant un ré-upload._
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 13 Supprimer une image d'un item
### Tâches
* ✅ Le créateur d'une liste peut supprimer l'image de ses items
* ✅ 🟨 ~~Dans le cas d'une image locale, le fichier de l'image n'est pas supprimé~~
  * _Alors, si. Nous, on supprime le fichier puisque le nom du fichier est unique._
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 14 Partager une liste
### Tâches
* ✅ Une fois la liste entièrement saisie, le créateur peut la partager
* ✅ Le partage d'une liste génère une URL avec un token (jeton unique différent du token de modification) destiné à être envoyé aux futurs participants
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 15 Consulter les réservations d'une de ses listes avant échéance
### Tâches
* ✅ Le créateur d'une liste partagée peut consulter les réservations effectuées sur sa liste
* ✅ Seul l'état réservé ou non s'affiche avant la date d'échéance
* ✅ un cookie permet d'identifier le créateur de la liste qu'il soit authentifié ou non afin de cacher les noms des participants (seuls les participants voient les noms des autres participants). On suppose que le créateur accède à la liste avec son navigateur habituel (celui sur lequel il s'est déjà authentifié)
### Contributeurs
* [Alexis L.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=lopesvaz3u)
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 16 Consulter les réservations et messages d'une de ses listes après échéance
### Tâches
* ✅ Après la date d'échéance de la liste, le créateur authentifié d'une liste partagée peut consulter les réservations effectuées sur sa liste avec les noms des participants et les messages associés aux réservations
### Contributeurs
* [Alexis L.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=lopesvaz3u)
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

# Extensions
## 17 Créer un compte
### Tâches
* ✅ Tout utilisateur non inscrit peut créer un compte à l'aide d'un formulaire
* ✅ Il choisit alors un login et un mot de passe
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)

## 18 S'authentifier
### Tâches
* ✅ Un utilisateur inscrit peut s'authentifier
* ✅ Une variable de session permet de maintenir l'état authentifié
### Contributeurs
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)

## 19 Modifier son compte
### Tâches
* ✅ Un utilisateur authentifié peut modifier son compte
* ✅ Seul le login ne peut pas être modifié
* ✅ S'il modifie son mot de passe, il doit alors à nouveau s'authentifier
### Contributeurs
* [Alexis L.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=lopesvaz3u)
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 20 Rendre une liste publique
### Tâches
* ✅ Le créateur d'une liste peut la rendre publique
* ✅ Les listes publiques apparaissent dans la liste publique des listes de souhaits
### Contributeurs
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)

## 21 Afficher les listes de souhaits publiques
### Tâches
* ✅ Tout utilisateur non enregistré peut consulter la liste des listes de souhaits publiques à partir de la page d'accueil
* ✅ Seuls les titres de liste apparaissent
* Les listes en cours de création (non validées par leur créateur) et les listes expirées n'apparaissent pas
* ✅ Les listes sont triées par date d'expiration croissante
* ✅ Un clic sur une liste redirige vers l'affichage du détail de cette liste
* En option, peuvent s'ajouter une recherche par auteur ou par intervalle de date.
### Contributeurs
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)

## 22 Créer une cagnotte sur un item
### Tâches
* ⛔ Le créateur d'une liste peut ouvrir une cagnotte pour un de ses item

## 23 Participer à une cagnotte
### Tâches
* ⛔ Pour les items avec cagnotte, les participants peuvent choisir un montant de participation dont le maximum est le reste à payer

## 24 Uploader une image
### Tâches
* ✅ Le créateur d'une liste peut ajouter des images par upload.
* ✅ Le fichier de l'image est alors écrit sur le serveur.
* ✅ Une sécurisation empêche d'écraser une image existante et autorise uniquement les fichiers
images.
* ✅ L'upload de fichiers sensibles (PHP ou autres) est rendu impossible
### Contributeurs
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)

## 25 Créer un compte participant
### Tâches
* ✅ 🟨 La création d'un compte peut aussi être utile aux participants afin de consulter les
participations qu'ils ont saisies et de ne plus saisir leur nom lors d'une participation
  * _Il n'y a aucune différence entre un participant et un créateur, tout le monde peut créer un compte. On n'est pas identifié comme "créateur" sur l'ensemble du site, on ne peut être créateur que par rapport à une liste._

## 26 Afficher la liste des créateurs
### Tâches
* ✅ Tous les utilisateurs peuvent consulter la liste des créateurs qui ont au moins une liste
publique active jointe à leur compte.
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
* [Lucas K.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=KEMMLERLucas)

## 27 Supprimer son compte
### Tâches
* ✅ Tous les utilisateurs enregistrés peuvent supprimer leur compte
* ✅ La suppression de son compte entraîne la suppression des listes, des items et images, des
participations uniquement avant échéance et de tous les messages
### Contributeurs
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)

## 28 Joindre des listes à son compte
### Tâches
* ⛔ Un utilisateur authentifié peut joindre des listes existantes à son compte en fournissant leurs
tokens de modification
  * _Puisque nous avons choisi de ne rendre modifiable une liste que par son créateur, et qu'on ne peut créer une liste qu'en étant connecté, cette fonctionnalité n'a plus de sens._
* ✅ Quand un utilisateur authentifié crée une nouvelle liste, elle est automatiquement jointe à
son compte


## Autres tâches
* [Antoine C.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=ActxLeToucan)
  * Déploiement
  * Notifications
  * Tests
* [Paul T.](https://github.com/ActxLeToucan/S3B_MyWishList/commits?author=PaulTisserant)
  * CSS
  * Pages en dur