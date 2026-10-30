# Tic Tac Frigo

Tic Tac Frigo aide chaque utilisateur à suivre les aliments présents dans son réfrigérateur et leurs dates d'expiration.

## Language

**Produit**:
Un aliment identifié par son nom, indépendamment de son conditionnement et de sa date d'expiration.
_Avoid_: Article, référence

**Article du frigo**:
Une entrée de stock distincte associant un produit, une date d'expiration, une quantité et un conditionnement. Un même produit peut correspondre à plusieurs articles du frigo.
_Avoid_: Produit

**Quantité**:
Le nombre entier d'unités physiques disponibles pour un article du frigo. Elle ne représente ni un poids ni un volume.
_Avoid_: Poids, volume, conditionnement

**Conditionnement**:
Le type prédéfini et facultatif d'une unité physique comptée par la quantité, par exemple « pièce », « bouteille », « pot » ou « paquet ».
_Avoid_: Quantité
