# api_symfony
Un tutoriel pour se former à la création d'API avec symfony



-----------------------------------------------------------------------

  ## ETAPE 1  : INSTALLATION DU PROJET
  
          symfony new api --full
          
----------------------------------------------------------------------- 

## ETAPE 2  : PARAMETRAGE DE L'ENVIRONNEMENT BASE DE DONNÉES
      
  ### Avec le SGBDR phpmyadmin, créez une base de données "api_symfony"
  ### Ouvrez le dossier du projet dans votre EDI
  ### Dans le fichier .env faire apparaître 
      DATABASE_URL=mysql://utilisateur:@127.0.0.1:3306/api_symfony
      

----------------------------------------------------------------------

## ETAPE 3 : CREATION DES ENTITES POST ET COMMENT

  ### symfony console make:entity     ou     php bin/console make:entity
  
   Class name of the entity to create or update (e.g. FierceElephant):
   Post
   
   Pour ce qui est des attributs :
   
   title   string  255   no
   content  text  no
   
   Appuyer sur ENTER pour finir.
   
   ### symfony console make:entity     ou     php bin/console make:entity
   
   Class name of the entity to create or update (e.g. FierceElephant):
   Comment
   
   Pour ce qui est des attributs :
   
   username  string  255   no
   content  text  no
   
   Appuyer sur ENTER pour finir.
   
   ### symfony console make:entity     ou     php bin/console make:entity
   
   Class name of the entity to create or update (e.g. FierceElephant):
   Post
   
   comme nouvelle attribut:
   comments   OneToMany 
   
   répondez yes quand il vous demandera s'il faut créer un attribut posts dans la classe Comment
   cette attribut posts aura automatiquement le type ManyToOne
   
 ---------------------------------------------------------------------------------------------------
 ## ETAPE 4 : MIGRATIONS
   
    symfony console make:migration

    symfony console doctrine:migrations:migrate 

 WARNING! You are about to execute a database migration that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
 > 

[notice] Migrating up to DoctrineMigrations\Version20200718191831
[warning] Migration DoctrineMigrations\Version20200718191644 was executed but did not result in any SQL statements.
[notice] finished in 119.6ms, used 12M memory, 2 migrations executed, 3 sql queries

  
   
 -----------------------------------------------------------------------------------------------------
   
 ## ETAPE 6 : INSTALLATION DE FIXTURES ET INSERTION DANS LA BASE
  
  
   composer require --dev orm-fixtures
   
   ### Dans le répertoire DataFixtures vous trouverez un fichier AppFixtures.php:
   

    namespace App\DataFixtures;
    
    use \DateTime;
    
    use App\Entity\Post;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    
    use Doctrine\Common\Persistence\ObjectManager;

    class AppFixtures extends Fixture
    
    {
    
        public function load(ObjectManager $manager)
        
        {
        
            $d = new DateTime();
            
            for ($i = 0; $i < 20; $i++) {
            
                $post = new Post();
                
                $post->setTitle('titre du post n° '.$i);
                
                $post->setContent('bla bla bla bla bli bli bli bli');
                
                $post->setCreatedAt($d);
                
                $manager->persist($post);
                
            }

            $manager->flush();
        }
        
    }
    
La boucle permet d'instancier des objets Post Fake et les insérer en base de données


### Chargement et purge de la table post

php bin/console doctrine:fixtures:load

---------------------------------------------------------------------------------------------------------

# Nous sommes désormais prêt à exposer nos posts fake. Il s'agit maintenant d'exposer cette donnée via des url et des méthodes d'envoi:

GET POST PUT DELETE PATCH

Exemple:

Le projet front-end envoi une requête en POST sur l'URL /api/insertion 
L'api receptionne la route de la requête et la méthode utilisée pour déterminer la réponse JSON qu'elle va envoyer
Dans le cas qui nous interesse la réponse ne sera pas de la donnée en JSON mais un message de succés de l'insertion

Autre Exemple:

Le projet front-end envoi une requête en GET sur l'URL /api/all
L'api receptionne la route de la requête et la méthode utilisée pour déterminer la réponse JSON qu'elle va envoyer
Dans le cas qui nous interesse la réponse sera un fichier JSON contenant les données provenant du tableau issu de la requête mysql.
all nous laisse supposer que cette requête sera du type 

SELECT * FROM table WHERE 1

Mais vous vous n'en saurais rien car c'est le queryBuilder qui s'en occupe

   
  -------------------------------------------------------------------------------------------------------
  Pour savoir ce qu'il faut faire, il faut déjà comprendre ce que l'on veut faire.
  Notre but est d'obtenir des données spécifiques provenant de la DB au formats JSON pour s'en servir dans nos composants front-end.
  
  C'est ici qu'intervient la sérialisation
 
 ## QU'EST CE QUE LA SERIALISATION ?
 
 Maintenant que notre ressource est prête, il s'agit de commencer doucement avec deux actions : sérialiser et désérialiser notre ressource. Concrètement, il s'agit de passer du mode linéarisé (JSON ou XML) au format délinéarisé (objet). Pour bien comprendre, voyons schématiquement ce que nous cherchons à faire :
 
 ![SCHEMA1](https://github.com/JimHin/api_tutoriel/blob/master/serializer.png)
 
 
 
 ![SCHEMA](https://github.com/JimHin/api_tutoriel/blob/master/serializer_workflow.png)
 -------------------------------------------------------------------------------------------------------
  
  
   
   
   
   
   
   
   
   
   
   
   
   
   
   
