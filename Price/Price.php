<?php
class Price extends plxPlugin {
 
    public function __construct($default_lang){
    # Appel du constructeur de la classe plxPlugin (obligatoire)
    parent::__construct($default_lang);
    
    # Pour accéder à une page de configuration
    $this->setConfigProfil(PROFIL_ADMIN,PROFIL_MANAGER);
    # Déclaration des hooks

    $this->addHook('ThemeEndHead', 'ThemeEndHead');
    $this->addHook('AdminTopEndHead', 'AdminTopEndHead');
    $this->addHook('plxShowConstruct', 'plxShowConstruct');
    $this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
    $this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
    $this->addHook('plxShowPageTitle', 'plxShowPageTitle');
    $this->addHook('SitemapStatics', 'SitemapStatics');
    $this->addHook('Price', 'Price'); 

    } 

    public function AdminTopEndHead() { ?>
      <link rel="stylesheet" href="<?php echo PLX_PLUGINS ?>Price/app/style.min.css" media="screen"/>
      <?php
    }
    
    public function ThemeEndHead() {?>

    <link rel="stylesheet" href="<?php echo PLX_PLUGINS ?>Price/style.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.21/angular.min.js"></script> 

         <script>
            
            function OrderFormController($scope){

        $scope.services = [

        <?php

          $nb_options = floor(sizeof($this->aParams)/2); // nombre de commentaire
          $nb_options = $nb_options + 1;
      
        for($i=1; $i<$nb_options; $i++) { // boucle pour afficher les commentaires

          $option = $this->getParam('option'.$i);
          $price = $this->getParam('price'.$i);
          $active = $this->getParam('active'.$i);

          if(empty($active)) {$active = "true";}

          if(!empty($option)) { ?>
            {name: "<?php echo $option;?>",  price: <?php echo $price;?>, active:<?php echo $active;?>},
          <?php
          }// endif     
        }// endfor  ?>
        ];

        $scope.toggleActive = function(s){
          s.active = !s.active;
        };

        $scope.total = function(){

          var total = 0;

          angular.forEach($scope.services, function(s){
            if (s.active){
              total+= s.price;
            }
          });

          return total;
        };
      }
      </script> 

    <?php
    }

    public function Price() {

      $info = $this->getParam('mnuInfo');
            if(!empty($info)){
                echo '<p>'. $info .'</p>';//info haut de page
            }
      ?>      

      <div class="price" ng-app>
        <form ng-app ng-controller="OrderFormController">
          <ul>
            <li ng-repeat="service in services" ng-click="toggleActive(service)" ng-class="{active:service.active}">
              {{service.name}} <span>{{service.price}} €</span>
            </li>
          </ul>

          <div class="total">
            Total : <span>{{total()}} €</span>
          </div>
        </form>
      </div>

      <?php 
    }

   public function plxShowConstruct() {
        # infos sur la page statique
        $string  = "if(\$this->plxMotor->mode=='price') {";
        $string .= "    \$array = array();";
        $string .= "    \$array[\$this->plxMotor->cible] = array(
            'name'      => '".$this->getParam('mnuName')."',
            'menu'      => '',
            'url'       => 'price',
            'readable'  => 1,
            'active'    => 1,
            'group'     => ''
        );";
        $string .= "    \$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
        $string .= "}";
        echo "<?php ".$string." ?>";
    }


    public function plxMotorPreChauffageBegin() {
        $template = $this->getParam('template')==''?'static.php':$this->getParam('template');
        $string = "
        if(\$this->get && preg_match('/^price\/?/',\$this->get)) {
            \$this->mode = 'price';
            \$this->cible = '../../plugins/Price/static';
            \$this->template = '".$template."';
            return true;
        }
        ";
        echo "<?php ".$string." ?>";
    }

    public function plxShowStaticListEnd() {
        # ajout du menu pour accèder à la page de Price
        if($this->getParam('mnuDisplay')) {
            echo "<?php \$class = \$this->plxMotor->mode=='price'?'active':'noactive'; ?>";
            echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li><a class=\"static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?price').'\">".$this->getParam('mnuName')."</a></li>'); ?>";
        }
    }


    public function plxShowPageTitle() {
        echo '<?php
            if($this->plxMotor->mode == "price") {
                echo plxUtils::strCheck($this->plxMotor->aConf["title"]." - '.$this->getParam('mnuName').'");
                return true;
            }
        ?>';
    }

    public function SitemapStatics() {
        echo '<?php
        echo "\n";
        echo "\t<url>\n";
        echo "\t\t<loc>".$plxMotor->urlRewrite("?price")."</loc>\n";
        echo "\t\t<changefreq>monthly</changefreq>\n";
        echo "\t\t<priority>0.8</priority>\n";
        echo "\t</url>\n";
        ?>';
    } 
      
} // class Price
?>