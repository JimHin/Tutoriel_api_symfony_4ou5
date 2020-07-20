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
   
   createdAt datetime no
   
   Appuyer sur ENTER pour finir.
   
   ### symfony console make:entity     ou     php bin/console make:entity
   
   Class name of the entity to create or update (e.g. FierceElephant):
   Comment
   
   Pour ce qui est des attributs :
   
   username  string  255   no
   
   content  text  no
   
   createdAt datetime no
   
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
   
 ## ETAPE 5 : INSTALLATION DE FIXTURES ET CHARGEMENT DANS LA BASE
  
  
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
 
 ![SCHEMA1](https://github.com/JimHin/api_symfony/blob/master/public/serializer.png)
 
 

 -------------------------------------------------------------------------------------------------------

# ETAPE 6: DECLARATION DU CONTROLEUR

symfony console make:controller ApiController

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class ApiController extends AbstractController
    {
        /**
         * @Route("/api", name="api")
         * @return Response
         */
        public function index()
        {
            return $this->render('api/index.html.twig', [
                'controller_name' => 'ApiController',
            ]);
        }
    }
    
    
   Dans un premier temps il faut donner à la méthode index un paramètre PostRepository à traiter.
   Il faut également dire au contrôleur que cette méthode index ne marche que pour la route api/post avec la méthode GET
   Enfin il faudra lui demander d'executer un finAll() à partir du PostRepository passé en paramètre.
   on fera alors un Die and Dump de cette réponse de la DB
   
   Finalement on a :
   
    namespace App\Controller;

    use App\Repository\PostRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class ApiController extends AbstractController
    {
        /**
         * @Route("/api/post", name="api_post_index", methods = {"GET"})
         * @param PostRepository $postRepository
         * @return Response
         */
        public function index(PostRepository $postRepository)
        {
            $posts = $postRepository->findAll();
            dd($posts);
            return $this->render('api/index.html.twig', [
                'controller_name' => 'ApiController',
            ]);
        }
    }
   
  ### Que fait la fonction dd() ?
  ### Avec quel logiciel je peux tester des requêtes sur des routes http:// ?
  
-----------------------------------------------------------------------------------------------------
  
  ## ETAPE 7 : On teste la route http://localhost:8000/api/post

![postman](https://github.com/JimHin/api_symfony/blob/master/public/dd.png)


puis sur postman pour déclarer une collection de requête qui concernent les posts de l'application:


![postman](https://github.com/JimHin/api_symfony/blob/master/public/postman.png)



# C'est cool mais moi, mon framework front-end, il veut du json et pas le dump d'un tableau ?!!!

---------------------------------------------------------------------------------------------------------
   
 ## ETAPE 8 : ON MODIFIE UN PEU NOTRE CONTRÔLEUR POUR QU'IL RETOURNE DU JSON

On essai de passer des données déclarées en dur dans un premier temps:


        $json = json_encode([

            "nom" => "jim",
            
            "data" => "essai affichage"
            
        ]);

        dd($json, $posts);

![essaijsonbrut](https://github.com/JimHin/api_symfony/blob/master/public/jsonencode.png)

Bon ben ca marche, alors on va lui demander de retourner json_encode($posts)
la variable $posts à ce moment du programme est sensée contenir le tableau de données provenant de la DB.
Facile !!!


        $json = json_encode($post);

        dd($json);

![essaijsondyn](https://github.com/JimHin/api_symfony/blob/master/public/bug.png)

 Ben ca m'affiche un tableau avec plein d'objets vides ?!!!!
 Eh oui c'est la magie de l'encapsulation. On a déclaré nos attributs en private que ce soit dans la classe Post ou la classe Comment
 Il faut donc passer par les getters pour accéder à la valeur de ces attributs.
 ### Essayez de passer ne serait-ce qu'un attribut de la classe Post en public et vous le verrais apparaître dans le JSON.
 
 ![essaijsondyn](https://github.com/JimHin/api_symfony/blob/master/public/public.png)
 
 Evidemment ce n'est pas ce que l'on va faire. Sécurité oblige.
   
--------------------------------------------------------------------------------  
   
 ## ETAPE 9 : LA NORMALISATION

Voici un Schéma plus détaillé de la serialisation :

![SCHEMA](https://github.com/JimHin/api_symfony/blob/master/public/serializer_workflow.png)

En réalité pour sérialiser il faut passer par la transformation de la donnée provenant de DB en un tableau associatif qu'on pourra dès lors encoder en JSON.
Voici les étapes dans le sens back-end vers front-end qui est celui qui nous intéresse pour le moment:

- instanciation et hydratation d'un objet Post (grâce à PostRepository)
- requête à la DB                                 **objet contenant la réponse de la DB**
- normalisation de cet objet                      **Tableau associatif**
- encodage en JSON de ce tableau associatif       **données au format JSON**
- return de ce JSON à la requête                  **réponse donnée au front-end**


      public function index(PostRepository $postRepository, NormalizerInterface $normalizer)
            {
                $posts = $postRepository->findAll();

                $post_normalize = $normalizer->normalize($posts);

                dd($post_normalize);


 On oubli surtout pas de signaler au contrôleur dans quel espace de nom se trouve NormalizerInterface
                 
                 use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
                 
                 
 On teste à nouveau la route http://localhost:8000/api/post
   
  ![normalisation](https://github.com/JimHin/api_symfony/blob/master/public/normalize.png) 
  
  -------------------------------------------------------------------------------------------------
  
  ## ETAPE 9 : LA CREATION DE GROUPES DE DONNÉES
  
  
  Cela se fait grâce à la règle @Groups au niveau de la clé primaire
  
   
       class Post
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         * @Groups("post:read")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=255)
         * @Groups("post:read")
         */
        private $title;

        /**
         * @ORM\Column(type="text")
         * @Groups("post:read")
         */
        private $content;

        /**
         * @ORM\Column(type="datetime")
         * @Groups("post:read")
         */
        private $createdAt;

        /**
         * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post")
         */
        private $comments;
        
        
 ICI SEULE LES VALEURS DES ATTRIBUTS TAGGÉS post:read SERONT EXPOSÉES ET CE POUR EVITER L'ERREUR DE RÉFÉRENCE CIRCULAIRE 
  (Apparement pour nous à ce stade l'ORM coorige cette erreur seule désormais. Ce qui veut dire que les développeurs exclues les clés étrangères sans qu'on ait forcémment à se servir des groupes pour les exclure.)
   
