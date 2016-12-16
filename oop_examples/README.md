OOP Examples
=======================

http://drupal.org/project/oop_examples

The project provides examples of object-oriented programming (OOP) in Drupal
starting from very basic ones. The examples are in sequence: each next
example improves the previous one.

OOP Examples 01 - 03 are applicable to Drupal 7 only and not present here,
because Drupal 8 has built-in PSR-4 support.

Examples available:

OOP Example 04. PSR-4 Namespaces.
PSR-4 is current Drupal 8 style of module namespaces. See 
https://www.drupal.org/node/2156625. Base class Vehicle and derived classes 
Car and Motorcycle have been created.

OOP Example 05. Business logic setup.
Class folders and namespaces structure has been created for further
business logic development.

OOP Example 06. Class field $color. Class constructor.
Class field $color has been added for Vehicle class. Default color
with translation t() is set up in class constructor because expression
is not allowed as field default value. Common method getDescription()
has been introduced.

OOP Example 07. Class field $doors.
Class field $doors has been added for Car class. Some car model derived
classes (Toyota) have been added.

OOP Example 08. ColorInterface.
Interface ColorInterface has been added. Two different class hierarchies:
Vehicle and Fruit implement this interface.

OOP Example 09. More interfaces.
Interfaces DriveInterface and JuiceInterface have been added for class
hierarchies Vehicle and Fruit respectively.

OOP Example 10. Interface Inheritance.
Two inherited interfaces have been made. VehicleInterface is a combination
of ColorInterface and DriveInterface. FruitInterface is a combination
of ColorInterface and JuiceInterface.

OOP Example 11. Dependency injection.
Class Driver has been introduced. Vehicle dependency is provided through
a class constructor. The dependency is passed as a DriveInterface, so future
implementations of the DriveInterface could be passed as well. For example,
Donkey or SpaceShip.

OOP Example 12. Factory.
Factory is an object, which creates another objects. This example provides
a ColorableFactory, which creates classes supporting ColorInterface.




________________________________

Créer une entité Drupal 8 en 10 secondes top chrono
Partager sur

    TwitterFacebookGoogle+

Compteur vitesse voiture
19/10/2014
Thème
Drupal 8
Modules
Développement

Dans un précédent billet, nous avons découvert le projet Console qui permet d'automatiser la création de modules Drupal 8 et d'autres taches récurrentes. Découvrons ensemble quelques autres fonctionnalités très intéressantes.

Les entités sont au coeur de l'architecture Drupal, et encore plus dans Drupal 8 où tout (ou presque) est une entité. Les Noeuds, Users, Termes de taxonomies (pour ne citer que les plus connues) de Drupal sont des entités. Créer une nouvelle entité peut permettre de répondre à des fonctionnalités spécifiques en implémentant au sein de celle-ci sa propre logique métier, et / ou encore de répondre à une problématique de performance en implémentant dans la table de base de l'entité toutes ses propriétés métier, évitant ainsi de nombreuses et coûteuses requêtes pour générer et rendre une (ou plusieurs dizaines de milliers) instance de cette entité.
Création d'une entité Drupal 8 en 10 secondes

Si vous n'avez pas encore installé le projet Console, je vous invite à consulter ce billet Créer un module Drupal 8 en moins de 30 secondes. Nous vous attendons.

Executons la commande suivante
cd /path/to/drupal8folder
bin/console generate:entity:content

 

Nous déclarons notre nouvelle entité dans notre prédécent module créé, intitulé Example, et donnons un nom à notre entité et sa classe.

En 3 questions, et moins de 10 secondes, nous venons de générer tout le code pour implémenter une nouvelle entité.

Regardons de plus près le résultat obtenu.

structure folder of an drupal 8 entity

Outre avoir mis à jour le fichier de déclaration des routes (example.routing.yml) et les fichiers de lien (example.links.menu.yml, etc), la commande a généré les fichiers suivants :

    src/NoteInterface.php qui déclare l'interface de notre entité
    src/NoteAccessControlHandler.php qui contrôle les droits d'accès à notre entité
    src/Entity/Note.php qui définit la classe de notre entité
    src/Entity/Form/NoteSettingsForm.php qui définit le formulaire de paramètres de l'entité
    src/Entity/Form/NoteForm.php qui définit le formulaire de création ou d'édition de l'entité
    src/Entity/Form/NoteDeleteForm.php qui définit le formulaire de suppression de l'entité
    src/Entity/Controller/NoteListController.php qui fournit la liste de nos entités créées

Activons notre module pour consulter les différentes interfaces fournies

Formulaire de gestion des champs de notre entité Drupal 8

Nous pouvons ajouter autant de champs que nécessaire à notre nouvelle entité et également gérer l'affichage du formulaire et/ou celui du rendu depuis les onglets correspondants.

Liste des nos entités drupal 8 créées

Nous pouvons consulter, créer, modifier ou supprimer nos entités depuis les chemins fournis par défaut, que nous pouvons bien sûr modifier depuis le fichier example.routing.yml

Bref nous disposons désormais de notre propre entité qui bénéficie de base de toute la puissance de Drupal 8 au travers de son API, notamment la field API et la form API.
Intégration de notre entité drupal 8 avec Views

Console ne propose pas (pour l'instant) "out of the box" l'intégration de notre nouvelle entité avec Views. (mise à jour du 24/10/2014 : Console propose depuis la version 0.2.16 l'intégration avec Views des entités générées. Cf. Integration with Views for entity content generated)

Il nous suffit d'implémenter \Drupal\views\EntityViewsDataInterface dans notre Classe Note et de le déclarer dans les annotations au moyen de la propriété views_data.

Dans notre fichier Note.php généré par Console, ajoutons cette ligne

"views_data" = "Drupal\example\Entity\NoteViewsData",

Pour obtenir les annotations suivantes

 /**
 * @file
 * Contains Drupal\example\Entity\Note.
 */

namespace Drupal\example\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\example\NoteInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Note entity.
 *
 * @ingroup example
 *
 * @ContentEntityType(
 *   id = "note",
 *   label = @Translation("Note entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\example\Entity\Controller\NoteListController",
 *     "views_data" = "Drupal\example\Entity\NoteViewsData",
 *
 *     "form" = {
 *       "add" = "Drupal\example\Entity\Form\NoteForm",
 *       "edit" = "Drupal\example\Entity\Form\NoteForm",
 *       "delete" = "Drupal\example\Entity\Form\NoteDeleteForm",
 *     },
 *     "access" = "Drupal\example\NoteAccessControlHandler",
 *   },
 *   base_table = "note",
 *   admin_permission = "administer Note entity",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "note.edit",
 *     "admin-form" = "note.settings",
 *     "delete-form" = "note.delete"
 *   },
 *   field_ui_base_route = "note.settings"
 * )
 */ 

Et créons notre fichier NoteViewsData.php (dans le répertoire src/Entity) qui va implémenter EntityViewsDataInterface, et étendre EntityViewsData, pour disposer automatiquement des champs de base de notre entité (id, name, uuid, etc.). Vous trouverez toute les explications et plus encore dans la documentation de l'Entity API de Drupal 8.

 /**
 * @file
 * Contains Drupal\example\Entity\NoteViewsData.
 */

namespace Drupal\example\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the node entity type.
 */
class NoteViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['note']['table']['base'] = array(
      'field' => 'id',
      'title' => t('Note'),
      'help' => t('The note entity ID.'),
    );

    return $data;
  }
} 

Notre entité est alors disponible en tant que telle dans Views. Ainsi que tous les champs qui lui seront ajoutés (dans l'exemple ci-dessous un champ décimal intitulé Note a été rajouté à notre entité)

Views et notre custom entité drupal 8

 

Views et notre entité drupal 8

Pour finir l'intégration complète de notre entité avec Views, et pouvoir par exemple utiliser le mode de rendu de notre entité pour l'affichage de la vue, il nous faut fournir un template à notre entité. Nous allons donc modifier notre fichier example.module pour implémenter la fonction hook_theme.

 /**
 * Implements hook_theme().
 */
function example_theme()
{
  $theme = [];
  $theme['note'] = array(
    'render element' => 'elements',
    'file' => 'note.page.inc',
    'template' => 'note',
  );

  return $theme;
} 

Nous implémentons avec cette fonction le template note.html.twig et nous utilisons le fichier note.page.inc pour fournir les variables à notre template. Créons nos deux fichiers, note.html.twig dans le répertoire template de notre module, et note.page.inc à la racine.

 {#
/**
 * @file note.html.twig
 * Default theme implementation to present note data.
 *
 * This template is used when viewing a note entity's page,
 *
 *
 * Available variables:
 * - content: A list of content items. Use 'content' to print all content, or
 * - attributes: HTML attributes for the container element.
 *
 * @see template_preprocess_note()
 *
 * @ingroup themeable
 */
#}
<div {{ attributes.addClass("note") }}>
  {% if content %}
    {{- content -}}
  {% endif %}
</div>

Et le fichier note.page.inc

 /**
 * @file note.page.inc
 * Note page callback file for the note entity.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for note templates.
 *
 * Default template: note.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_note(&$variables) {

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
} 

Notre entité est désormais pleinement opérationnelle.
Ajouter des propriétés à une entité Drupal 8 spécifique

Pour aller un peu plus loin encore, nous allons ajouter quelques propriétés spécifiques à notre table de base de notre entité. Ce cas de figure peut être intéressant pour des raisons de performance si vous prévoyez d'avoir plusieurs dizaines de milliers (ou plus) de lignes à rendre. Ainsi les valeurs seront lues directement depuis la table de base évitant de couteuses jointures dans la requête.

Ajoutons à notre entité trois propriétés : une note littérale, une appréciation littérale et enfin l'élève (sous la forme d'un champ Entity Reference ciblant les utilisateurs enregistrés). Pour ce nous les déclarons dans la fonction baseFieldDefinitions de notre fichier Note.php implémentant la classe de notre entité.

   /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Note entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Note entity.'))
      ->setReadOnly(TRUE);


    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Note entity.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Litteral Note field.
    // ListTextType with a drop down menu widget.
    // The values shown in the menu are 'A', 'B', 'C' and 'D'.
    // In the view the field content is shown as string.
    // In the form the choices are presented as options list.
    $fields['litteral_note'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Litteral Note'))
      ->setDescription(t('The litteral Note which evaluate the student.'))
      ->setSettings(array(
        'allowed_values' => array(
          'a' => 'A',
          'b' => 'B',
          'c' => 'C',
          'd' => 'D',
        ),
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      // Litteral appreciation field.
      // We set display options for the view as well as the form.
      // Users with correct privileges can change the view and edit configuration.
      $fields['litteral_appreciation'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Litteral appreciation'))
        ->setDescription(t('The Litteral appreciation of the student.'))
        ->setSettings(array(
          'default_value' => '',
          'max_length' => 255,
          'text_processing' => 0,
        ))
        ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'string',
          'weight' => -6,
        ))
        ->setDisplayOptions('form', array(
          'type' => 'string',
          'weight' => -6,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

        // Student ID field.
        // Entity reference field, holds the reference to the user object.
        // The view shows the user name field of the user.
        // The form presents a auto complete field for the user name.
        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('Student Name'))
          ->setDescription(t('The Name of the student.'))
          ->setSetting('target_type', 'user')
          ->setSetting('handler', 'default')
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'entity_reference',
            'weight' => -3,
          ))
          ->setDisplayOptions('form', array(
            'type' => 'entity_reference_autocomplete',
            'settings' => array(
              'match_operator' => 'CONTAINS',
              'size' => 60,
              'autocomplete_type' => 'tags',
              'placeholder' => '',
            ),
            'weight' => -3,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Note entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  } 

Ces 3 propriétés seront configurables dans le formulaire de création et le rendu de l'entité grâce aux options setDisplayConfigurable('form', TRUE) et setDisplayConfigurable('view', TRUE).

Drupal fournit de base les types de champs suivants. En complément, les modules peuvent fournir d'autres types de champs qui peuvent alors être utilisés également.

    string: un champ text simple.
    boolean: une valeur booléenne stockée comme un entier.
    integer: un entier, avec des paramètres minimum et maximum possible pour la validation du champ (disponibles aussi pour les champs de type decimal et float)
    decimal: une valeur décimal, avec une précision configuration
    float: un chiffre à virgule flottante
    language: contient le code du language et les différentes propriétés du language
    timestamp: une valeur Unix timestamp stockée comme un entier
    created: un timestamp qui utilise la date actuelle comme valeur par défaut
    changed: un timestamp qui est automatiquement mis à jour sur la date actuelle si l'entité est sauvé.
    date: une date stockée selon le format ISO 8601.
    uri: ce champ contient une uri. Le module link fournit aussi un champ de type lien qui peut inclure un titre de lien et peut pointer vers une adresse/route interne ou externe
    uuid:  un champ UUID qui génère un nouveau UUID comme valuer par défaut
    email: Un champ e-mail, avec la validation correspondante et les widgets et formateurs associés
    entity_reference: un champ entity reference avec un target_id et un champ calculé contenant les propriétés du champ. Le module entity reference fournit les widgets et formateurs quand il est activé.
    map: peut contenir n'importe quelle quantité de propriétés arbitraires, stockées dans une chaine de texte sérialisée.

Pour activer ces trois nouveaux champs, il faut bien sûr désinstaller / réinstaller notre module s'il était déjà activé, afin de relancer le processus de création de notre entité, et que nos nouveaux champs soient créés au niveau de la table de base de l'entité.

table mysql de notre entité

Notre table contient désormais bien nos nouveaux champs.

configuration de notre formulaire de saisie pour l'entité

Et nous pouvons bien sûr paramétrer notre formulaire de saisie au moyen de l'interface, aussi bien que le rendu de notre entité depuis l'onglet Manage Display ou Gérer l'Affichage si vous avez choisi le français lors de l'installation de Drupal 8.

Nous disposons alors d'une entité sur mesure, dont les champs spécifiques sont stockés dans la même table de la base de données et bien sûr accessibles depuis Views.

formulaire de saisie de notre entité

intégration des champs spécifiques de notre entité dans Views

Il ne restera plus, au niveau de l'intégration de notre entité dans Views, qu'à faire la jointure entre le champ spécifique Student Name et la table des utilisateurs pour pouvoir récupérer toutes leurs informations, si nécessaire.
En guise de conclusion

Le module Console nous permet à la fois de gagner du temps et de disposer d'une base solide et saine pour commencer à implémenter des propriétés ou des logiques métier. Et de par sa nouvelle API et son approche objet, Drupal 8 semble encore plus redoutable que son prédécesseur, pourtant déjà bien armé, et donne encore plus de sens à cette maxime.

    Les seules limites de Drupal 8 seront celles de notre imagination.

Au final, cela nous aura pris peut-être un peu plus que 10 secondes, mais pas pour la génération de notre entité brute. Nous sommes d'accord ?
