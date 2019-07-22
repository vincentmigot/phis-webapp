<?php
//******************************************************************************
//                            YiiProjectModel.java
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: Mar, 2017
// Contact: morgane.vidal@inra.fr,arnaud.charleroy@inra.fr, anne.tireau@inra.fr, 
//          pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSProjectModel;

use Yii;

/**
 * The Yii model for the Projects. Used with web services
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSProjectModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @update [Arnaud Charleroy] 14 September, 2018 : change the value of website attribute from ""  
 *                                                 to null because of the webservice rules validation
 */
class YiiProjectModel extends WSActiveRecord {

    /**
     * the project's uri
     *  (e.g. http://www.phenome-fppn.fr/diaphen/DROPS)
     * @var string
     */
    public $uri;
    const URI = "uri";
    const LABEL = "label";
    /**
     * the project's name
     *  (e.g. DROught-tolerant yielding PlantS)
     * @var string
     */
    public $name;
    const NAME = "name";
    /**
     * the project's acronyme. Used to generate the project's uri
     *  (e.g. DROPS)
     * @var string
     */
    public $shortname;
    const SHORTNAME = "shortname";
    
    /**
     * The list of projects related to the current project.
     * @var array 
     */
    public $relatedProjects;
    const RELATED_PROJECTS = "relatedProjects";
    
    /**
     * The financial support of the current project.
     * @var 
     */
    public $financialSupport;
    const FINANCIAL_SUPPORT = "financialSupport";
    
    /**
     * The financial reference of the current project.
     * @var string
     */
    public $financialReference;
    const FINANCIAL_REFERENCE = "financialReference";
    
    /**
     * The description of the current project.
     * @var string
     */
    public $description;
    const DESCRIPTION = "description";
    
    /**
     * The date start of the project.
     * @var string
     */
    public $startDate;
    const DATE_START = "startDate";
    
    /**
     * The date end of the project
     * @var string
     */
    public $endDate;
    const DATE_END = "endDate";
    
    /**
     * The keywords of the project.
     * @var array
     */
    public $keywords = [];
    const KEYWORDS = "keywords";
    
    /**
     * The home page of the project.
     * @var string
     */
    public $homePage;
    const HOME_PAGE = "homePage";
    
    /**
     * The administrative contacts of the project.
     * @var array
     */
    public $administrativeContacts;
    const ADMINISTRATIVE_CONTACTS = "administrativeContacts";
    const FIRSTNAME = "firstname";
    const LASTNAME = "lastname";
    const EMAIL = "email";
    
    /**
     * The project coordinators.
     * @var array
     */
    public $projectCoordinatorContacts;
    const PROJECT_COORDINATORS = "coordinators";
    
    /**
     * The scientific contacts of the project.
     * @var array
     */
    public $scientificContacts;
    const SCIENTIFIC_CONTACTS = "scientificContacts";
    
    /**
     * The objective of the project.
     * @var string
     */
    public $objective;
    const OBJECTIVE = "objective";    
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSProjectModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }
    
    public function rules() {
        return [
           [['uri', 'dateStart', 'dateEnd', 'name', 'shortname'], 'required'],
           [['uri', 'name', 'shortname', 'dateStart', 'dateEnd', 
               'scientificContacts', 'administrativeContacts', 'projectCoordinatorContacts', 'relatedProjects'], 'safe'],
           [['description'], 'string'],
           [['uri', 'homePage'], 'string', 'max' => 300],
           [['homePage'],'url'],
           [['keywords'], 'string', 'max' => 500],
           [['name', 'shortname', 'financialSupport', 'financialReference'], 'string', 'max' => 200],
           [['objective'], 'string', 'max' => 256]
        ];
    }
    
    public function attributeLabels() {
        return [
          'uri' => 'URI',
          'name' => Yii::t('app', 'Name'),
          'shortname' => Yii::t('app', 'Shortname'),
          'relatedProjects' => Yii::t('app', 'Related Projects'),
          'financialSupport' => Yii::t('app', 'Financial Support'),
          'financialReference' => Yii::t('app', 'Financial Reference'),
          'description' => Yii::t('app', 'Description'),
          'dateStart' => Yii::t('app', 'Date Start'),
          'dateEnd' => Yii::t('app', 'Date End'),
          'keywords' => Yii::t('app', 'Keywords'),
          'homePage' => Yii::t('app', 'Homepage'),
          'administrativeContacts' => Yii::t('app', 'Administrative Contacts'), 
          'projectCoordinatorContacts' => Yii::t('app', 'Project Coordinators'),
          'scientificContacts' => Yii::t('app', 'Scientific Contacts'),
          'objective' => Yii::t('app', 'Objective')
        ];
    }
    
    /**
     * Fill the attributes of the project from the content of the array given in parameter. 
     * comprises dans le tableau passé en paramètre
     * @param array $array tableau clé => valeur contenant les valeurs des attributs du projet
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiProjectModel::URI];
        $this->name = $array[YiiProjectModel::NAME];
        $this->shortname = $array[YiiProjectModel::SHORTNAME];
        
        if (isset($array[YiiProjectModel::FINANCIAL_SUPPORT])) {
            $this->financialSupport->uri = $array[YiiProjectModel::FINANCIAL_SUPPORT]->uri;
            $this->financialSupport->label = $array[YiiProjectModel::FINANCIAL_SUPPORT]->label;
        }
        
        if (isset($array[YiiProjectModel::RELATED_PROJECTS])) {
            foreach($array[YiiProjectModel::RELATED_PROJECTS] as $relatedProject) {
                $newRelatedProject->uri = $relatedProject[YiiProjectModel::URI];
                $newRelatedProject->label = $relatedProject[YiiProjectModel::LABEL];
                $this->relatedProjects[] = $newRelatedProject;
            }
        }
        
        $this->financialReference = $array[YiiProjectModel::FINANCIAL_REFERENCE];
        $this->description = $array[YiiProjectModel::DESCRIPTION];
        $this->startDate = $array[YiiProjectModel::DATE_START];
        $this->endDate = $array[YiiProjectModel::DATE_END];
        
        if (isset($array[YiiProjectModel::KEYWORDS])) {
            foreach ($array[YiiProjectModel::KEYWORDS] as $keyword) {
                $this->keywords[] = $keyword;
            }
        }
        
        $this->homePage = $array[YiiProjectModel::HOME_PAGE];
        $this->objective = $array[YiiProjectModel::OBJECTIVE];
        
        if (isset($array[YiiProjectModel::ADMINISTRATIVE_CONTACTS])) {
            foreach ($array[YiiProjectModel::ADMINISTRATIVE_CONTACTS] as $administrativeContact) {
                $newAdministrativeContact->uri = $administrativeContact->uri;
                $newAdministrativeContact->firstname = $administrativeContact->firstname;
                $newAdministrativeContact->lastname = $administrativeContact->lastname;
                $newAdministrativeContact->email = $administrativeContact->email;
                
                $this->administrativeContacts[] = $newAdministrativeContact;
            }
        }
        
        if (isset($array[YiiProjectModel::PROJECT_COORDINATORS])) {
            foreach ($array[YiiProjectModel::PROJECT_COORDINATORS] as $projectCoordinator) {
                $newProjectCoordinator->uri = $projectCoordinator->uri;
                $newProjectCoordinator->firstname = $projectCoordinator->firstname;
                $newProjectCoordinator->lastname = $projectCoordinator->lastname;
                $newProjectCoordinator->email = $projectCoordinator->email;
                
                $this->projectCoordinatorContacts[] = $newProjectCoordinator;
            }
        }
        
        if (isset($array[YiiProjectModel::SCIENTIFIC_CONTACTS])) {
            foreach ($array[YiiProjectModel::SCIENTIFIC_CONTACTS] as $scientificContact) {
                $newScientificContactContact->uri = $scientificContact->uri;
                $newScientificContactContact->firstname = $scientificContact->firstname;
                $newScientificContactContact->lastname = $scientificContact->lastname;
                $newScientificContactContact->email = $scientificContact->email;
                
                $this->scientificContacts[] = $newScientificContactContact;
            }
        }
    }
    
    /**
     * @return array contenant l'élément à enregistrer en base de données
     *         cette méthode est publique pour que l'utilisateur puisse choisir de l'utiliser 
     *         ou d'envoyer lui-même son propre tableau (dans le cas où il souhaite enregistrer plusieurs instances)
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiProjectModel::URI] = $this->uri;
        $elementForWebService[YiiProjectModel::NAME] = $this->name;
        $elementForWebService[YiiProjectModel::SHORTNAME]= $this->shortname;
        
        if ($this->relatedProjects != null) {
            foreach ($this->relatedProjects as $relatedProject) {
                $elementForWebService[YiiProjectModel::RELATED_PROJECTS] = $relatedProject;
            }
        }
        
        $elementForWebService[YiiProjectModel::FINANCIAL_SUPPORT] = $this->financialSupport;
        $elementForWebService[YiiProjectModel::FINANCIAL_REFERENCE] = $this->financialReference;
        $elementForWebService[YiiProjectModel::DATE_START] = $this->startDate;
        $elementForWebService[YiiProjectModel::DATE_END] = $this->endDate;
        
        if ($this->keywords != null) {
            foreach ($this->keywords as $keyword) {
                $elementForWebService[YiiProjectModel::KEYWORDS][] = $keyword;
            }
        }
        
        $elementForWebService[YiiProjectModel::DESCRIPTION] = $this->description;
        $elementForWebService[YiiProjectModel::OBJECTIVE] = $this->objective;

        //SILEX:info
        // Unlike the other text attributes (description, keywords, etc.) 
        // of the project model, the website attribute must be a URL.
        // Due to format validations of the Web Service (@URL), 
        // send the website attribute with an empty string value
        // will raise an 400 error like:
        // [postProject.arg0[0].website]website is not an URL  | Invalid value
        //\SILEX:info
        $elementForWebService[YiiProjectModel::HOME_PAGE] = ($this->homePage === "") ? null : $this->homePage;
        
        if ($this->administrativeContacts != null) {
            foreach ($this->administrativeContacts as $administrativeContact) {
               $elementForWebService[YiiProjectModel::ADMINISTRATIVE_CONTACTS][] = $administrativeContact;
            }
        }
        
        if ($this->projectCoordinatorContacts != null) {
            foreach ($this->projectCoordinatorContacts as $projectCoordinator) {
                $elementForWebService[YiiProjectModel::PROJECT_COORDINATORS][] = $projectCoordinator;
            }
        }
        
        if ($this->scientificContacts != null) {
            foreach ($this->scientificContacts as $scientificContact) {
               $elementForWebService[YiiProjectModel::SCIENTIFIC_CONTACTS][] = $scientificContact;
            }
        }
        
        return $elementForWebService;
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param string $uri
     * @return mixed l'objet s'il existe, un message sinon
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $requestRes = $this->wsModel->getProjectByURI($sessionToken, $uri, $params);
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
}
