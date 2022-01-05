# Participant
## 1 Afficher une liste de souhaits
* ✅ L'affichage du détail d'une liste présente toutes les informations de la liste accompagnées de la liste des items
* Chaque item est affiché avec son nom, son image et l'état de la réservation
  * _Ajouter réservations dans la BDD_
* L'affichage de l'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant et les messages n'apparaissent pas avant la date d'échéance
* ✅ un clic sur un item donne accès à son détail
* Pour afficher une liste, il faut connaître son URL contenant un token

## 2 Afficher un item d'une liste
* L'affichage d'un item présente toutes ses informations détaillées, son image, et l'état de la réservation (nom du participant sans message)
  * _Ajouter réservations dans la BDD_
* L'état de la réservation est restreint pour le propriétaire de la liste (basé sur un cookie) : le nom du participant n’apparaît pas
* Un item appartenant à aucune liste validée (par son créateur) ne peut pas être affiché
  * _Qu'est ce qu'une liste **validée** ?_
* Pour afficher un item d'une liste, il faut connaître l'URL de sa liste contenant un token

## 3 Réserver un item
* Dans la page de l'item, si l'item n'est pas réservé, un formulaire permet de saisir le nom du participant
* La validation du formulaire enregistre la participation
* Le nom du participant peut être mémorisé dans une variable de session ou un cookie pour pré-remplir le champ afin de ne pas avoir à le retaper

## 4 Ajouter un message avec sa réservation
* Dans la page de l'item, si l'item n'est pas réservé, le formulaire de participation permet également de saisir un message destiné le créateur* La validation du formulaire enregistre le message avec la participation

## 5 Ajouter un message sur une liste
* Dans la page d'une liste, un formulaire permet d'ajouter un message public rattaché à la liste
* Les messages sur la liste seront affichés avec le détail de la liste

# Créateur
## 6 Créer une liste
* ✅ Un utilisateur non authentifié peut créer une nouvelle liste de souhaits
* ✅ Un formulaire lui permet de saisir les informations générales de la liste
* ✅ les informations sont : titre, description et date d'expiration
* ✅ Les balises HTML sont interdites dans ces champs
* Lors de sa création un token est créé pour accéder à cette liste en modification

## 7 Modifier les informations générales d'une de ses listes
* Le créateur d'une liste peut modifier les informations générales de ses listes
* Pour modifier il doit connaître son URL de modification (avec token)

## 8 Ajouter des items
* Le créateur d'une liste peut ajouter des items à une de ses listes après l'avoir sélectionnée par son URL de modification (avec token)
* ✅ Un formulaire permet de saisir les informations de l'item
* ✅ les informations sont : nom et description et prix
* ✅ il peut aussi fournir l'URL d'une page externe qui détaille le produit (sur un site de ecommerce par exemple)

## 9 Modifier un item
* Le créateur d'une liste peut modifier les informations des items de ses listes
* Une fois réservé, un item ne peut plus être modifié

## 10 Supprimer un item
* Le créateur d'une liste peut supprimer un item d'un de ses listes si il n'est pas reservé

## 11 Rajouter une image à un item
* Le créateur d'une liste peut ajouter une image à un de ses items
* Pour cela il fournit l'URL complète d'une image externe (acceptant le hot-linking) ou bien le chemin relatif d'une image déjà présente dans le dossier web/img/

## 12 Modifier une image d'un item
* Le créateur d'une liste peut modifier l'URL de l'image de ses items

## 13 Supprimer une image d'un item
* Le créateur d'une liste peut supprimer l'image de ses items
* Dans le cas d'une image locale, le fichier de l'image n'est pas supprimé

## 14 Partager une liste
* Une fois la liste entièrement saisie, le créateur peut la partager
* Le partage d'une liste génère une URL avec un token (jeton unique différent du token de modification) destiné à être envoyé aux futurs participants

## 15 Consulter les réservations d'une de ses listes avant échéance
* Le créateur d'une liste partagée peut consulter les réservations effectuées sur sa liste
* Seul l'état réservé ou non s'affiche avant la date d'échéance
* un cookie permet d'identifier le créateur de la liste qu'il soit authentifié ou non afin de cacher les noms des participants (seuls les participants voient les noms des autres participants). On suppose que le créateur accède à la liste avec son navigateur habituel (celui sur lequel il s'est déjà authentifié)

## 16 Consulter les réservations et messages d'une de ses listes après échéance
* Après la date d'échéance de la liste, le créateur authentifié d'une liste partagée peut consulter les réservations effectuées sur sa liste avec les noms des participants et les message associés aux réservations