# station_meteo

Application de station météo personnelle

Fonctionnalités :

- Aquisition des données envoyées par les sondes
- Interface de visualisation des données


## Installation

1. Cloner le dépôt
2. Installer les dépendences php avec composer : ``composer isntall``
3. Créer la base de données
4. Lancer les migrations Doctrine pour créer les tables : ``php bin/console doctrine:migrations:migrate``
5. Créer manuellement un utilisateur admin en base de données.
