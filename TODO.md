# TODO.md - Plan pour ajouter relation Evenement <-> Inscription

## Steps à compléter:

### 1. [DONE ✅] Créer le fichier TODO.md
### 2. [DONE ✅] Éditer src/Entity/Evenement.php - Ajouter propriété $inscriptions, annotations ORM, __construct, getInscriptions, addInscription, removeInscription
### 3. [DONE ✅] Éditer src/Entity/Inscription.php - Ajouter propriété $evenement, annotations ORM, getEvenement, setEvenement
### 4. [DONE ✅] Générer migration Doctrine
### 5. [DONE ✅] Exécuter migration et clear cache
## COMPLET ✅

Toutes les étapes terminées. Relation inscriptions ajoutée avec succès: Evenement → Collection d'Inscriptions, bidirectionnelle.

Fichiers modifiés:
- src/Entity/Evenement.php (ajout $inscriptions + méthodes)
- src/Entity/Inscription.php (ajout $evenement + méthodes)
- Migration générée et appliquée
- Cache vidé

