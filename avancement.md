# Participant
## 1 Afficher une liste de souhaits
* ✅ L'affichage du détail d'une liste présente toutes les informations de la liste accompagnées de la liste des items
* ✅ Chaque item est affiché avec son nom, son image et l'état de la réservation
* ✅ L'affichage de l'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant et les messages n'apparaissent pas avant la date d'échéance
* ✅ un clic sur un item donne accès à son détail
* ✅ Pour afficher une liste, il faut connaître son URL contenant un token

## 2 Afficher un item d'une liste
* ✅ L'affichage d'un item présente toutes ses informations détaillées, son image, et l'état de la réservation (nom du participant sans message)
* ✅ L'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant n’apparaît pas
* ✅ Un item appartenant à aucune liste validée (par son créateur) ne peut pas être affiché
* ✅ Pour afficher un item d'une liste, il faut connaître l'URL de sa liste contenant un token

## 3 Réserver un item
* ✅ Dans la page de l'item, si l'item n'est pas réservé, un formulaire permet de saisir le nom du participant
* ✅ La validation du formulaire enregistre la participation
* ✅ Le nom du participant peut être mémorisé dans une variable de session ou un cookie pour pré-remplir le champ afin de ne pas avoir à le retaper

## 4 Ajouter un message avec sa réservation
* ✅ Dans la page de l'item, si l'item n'est pas réservé, le formulaire de participation permet également de saisir un message destiné le créateur
* ✅ La validation du formulaire enregistre le message avec la participation

## 5 Ajouter un message sur une liste
* ✅ Dans la page d'une liste, un formulaire permet d'ajouter un message public rattaché à la liste
* ✅ Les messages sur la liste seront affichés avec le détail de la liste

# Créateur
## 6 Créer une liste
* ✅ Un utilisateur non authentifié peut créer une nouvelle liste de souhaits
* ✅ Un formulaire lui permet de saisir les informations générales de la liste
* ✅ les informations sont : titre, description et date d'expiration
* ✅ Les balises HTML sont interdites dans ces champs
* ✅ Lors de sa création un token est créé pour accéder à cette liste en modification

## 7 Modifier les informations générales d'une de ses listes
* ✅ Le créateur d'une liste peut modifier les informations générales de ses listes
* ✅ Pour modifier il doit connaître son URL de modification (avec token)

## 8 Ajouter des items
* ✅ Le créateur d'une liste peut ajouter des items à une de ses listes après l'avoir sélectionnée par son URL de modification (avec token)
* ✅ 🟨 Un formulaire permet de saisir les informations de l'item
* ✅ 🟨 les informations sont : nom et description et prix
* ✅ 🟨 il peut aussi fournir l'URL d'une page externe qui détaille le produit (sur un site de ecommerce par exemple)
  * _Finalement, on crée un item par défaut, et si on veut ajouter des informations, on peut le modifier._

## 9 Modifier un item
* ✅ Le créateur d'une liste peut modifier les informations des items de ses listes
* ✅ Une fois réservé, un item ne peut plus être modifié

## 10 Supprimer un item
* ✅ Le créateur d'une liste peut supprimer un item d'un de ses listes si il n'est pas reservé

## 11 Rajouter une image à un item
* ✅ Le créateur d'une liste peut ajouter une image à un de ses items
* ✅ 🟨 ~~Pour cela il fournit l'URL complète d'une image externe (acceptant le hot-linking) ou bien le chemin relatif d'une image déjà présente dans le dossier web/img/~~
  * _On peut uploader des images, donc on utilise cette méthode._

## 12 Modifier une image d'un item
* ✅ 🟨 ~~Le créateur d'une liste peut modifier l'URL de l'image de ses items~~
  * _Le créateur d'une liste peut modifier l'image en faisait un réupload._

## 13 Supprimer une image d'un item
* ✅ Le créateur d'une liste peut supprimer l'image de ses items
* ✅ 🟨 ~~Dans le cas d'une image locale, le fichier de l'image n'est pas supprimé~~
  * _Alors, si. Nous on supprime le fichier puisque le nom du fichier est unique._

## 14 Partager une liste
* ✅ Une fois la liste entièrement saisie, le créateur peut la partager
* ✅ Le partage d'une liste génère une URL avec un token (jeton unique différent du token de modification) destiné à être envoyé aux futurs participants

## 15 Consulter les réservations d'une de ses listes avant échéance
* ✅ Le créateur d'une liste partagée peut consulter les réservations effectuées sur sa liste
* ✅ Seul l'état réservé ou non s'affiche avant la date d'échéance
* ✅ un cookie permet d'identifier le créateur de la liste qu'il soit authentifié ou non afin de cacher les noms des participants (seuls les participants voient les noms des autres participants). On suppose que le créateur accède à la liste avec son navigateur habituel (celui sur lequel il s'est déjà authentifié)

## 16 Consulter les réservations et messages d'une de ses listes après échéance
* ✅ Après la date d'échéance de la liste, le créateur authentifié d'une liste partagée peut consulter les réservations effectuées sur sa liste avec les noms des participants et les message associés aux réservations

# Extensions
## 17 Créer un compte
* ✅ Tout utilisateur non inscrit peut créer un compte à l'aide d'un formulaire
* ✅ Il choisit alors un login et un mot de passe
## 18 S'authentifier
* ✅ Un utilisateur inscrit peut s'authentifier
* ✅ Une variable de session permet de maintenir l'état authentifié

## 19 Modifier son compte
* ✅Un utilisateur authentifié peut modifier son compte
* ✅Seul le login ne peut pas être modifié
* ✅Si il modifie son mot de passe, il doit alors à nouveau s'authentifier

## 20 Rendre une liste publique
* Le créateur d'une liste peut la rendre publique
* Les listes publiques apparaissent dans la liste publique des listes de souhaits

## 21 Afficher les listes de souhaits publiques
* Tout utilisateur non enregistré peut consulter la liste des listes de souhaits publiques à partir de la page d'accueil
* Seuls les titres de liste apparaissent
* Les listes en cours de création (non validées par leur créateur) et les listes expirées n'apparaissent pas
* Les listes sont triées par date d'expiration croissante
* Un clic sur une liste redirige vers l'affichage du détail de cette liste
* En option, peuvent s'ajouter une recherche par auteur ou par intervalle de date.

## 22 Créer une cagnotte sur un item
* ⛔ Le créateur d'une liste peut ouvrir une cagnotte pour un de ses item

## 23 Participer à une cagnotte
* ⛔ Pour les items avec cagnotte, les participants peuvent choisir un montant de participation dont le maximum est le reste à payer

## 24 Uploader une image
* ✅ Le créateur d'une liste peut ajouter des images par upload.
* ✅ Le fichier de l'image est alors écrit sur le serveur.
* ✅ Une sécurisation empêche d'écraser une image existante et autorise uniquement les fichiers
images.
* ✅ Le upload de fichiers sensibles (PHP ou autres) est rendu impossible

## 25 Créer un compte participant
* ✅ La création d'un compte peut aussi être utile aux participants afin de consulter les
participations qu'ils ont saisies et de ne plus saisir leur nom lors d'une participation

## 26 Afficher la liste des créateurs
* Tous les utilisateurs peuvent consulter la liste des créateurs qui ont au moins une liste
publique active jointe à leur compte.

## 27 Supprimer son compte
* ✅ Tous les utilisateurs enregistrés peuvent supprimer leur compte
* ✅ La suppression de son compte entraîne la suppression des listes, des items et images, des
participations uniquement avant échéance et de tous les messages

## 28 Joindre des listes à son compte
* Un utilisateur authentifié peut joindre des listes existantes à son compte en fournissant leurs
tokens de modification
* ✅ Quand un utilisateurs authentifié crée une nouvelle liste, elle est automatiquement jointe à
son compte
