<?php

//**********************************************************************************************
//                                       DatasetController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  January, 15 2018 - creation with multiple variables 
//                          in csv file.
// Subject: implements the CRUD actions for WSDatasetModel
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\wsModels\WSProvenanceModel;
use app\models\wsModels\WSDataModel;
use openSILEX\handsontablePHP\adapter\HandsontableSimple;
use openSILEX\handsontablePHP\classes\ColumnConfig;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\YiiExperimentModel;
use app\models\yiiModels\YiiSensorModel;
use app\components\helpers\Vocabulary;

require_once '../config/config.php';

/**
 * CRUD actions for YiiDatasetModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDatasetModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DatasetController extends Controller {

    //SILEX:TODO
    //create a global configuration file for the csv files
    //\SILEX:TODO

    const AGRONOMICAL_OBJECT_URI = "ScientificObjectAlias";
    const DATE = "Date";
    const VALUE = "Value";
    const ERRORS_MISSING_COLUMN = "Missing Columns";
    const ERRORS_ROWS = "Rows";
    const ERRORS_LINE = "Line";
    const ERRORS_COLUMN = "Column";
    const ERRORS_MESSAGE = "Message";
    
    const SENSORS_DATA = "sensors";
    const SENSOR_DATA_URI = "sensorUri";
    const SENSOR_DATA_LABEL = "sensorLabel";
    const SENSOR_DATA_TYPE = "sensorType";

    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * transform a csv in JSON
     * @param array $csvContent the csv content to transform in json
     * @return string the csv content in json. Unescape the slashes in the csv
     */
    private function csvToArray($csvContent) {
        $arrayCsvContent = [];
        foreach ($csvContent as $line) {
            $arrayCsvContent[] = str_getcsv($line, Yii::$app->params['csvSeparator']);
        }
        return $arrayCsvContent;
    }

    /**
     * @return array contains the variables list. The key is the variable label 
     *               and the value is the variable label
     */
    private function getVariablesListLabelToShowFromVariableList($variables) {
        if ($variables !== null) {
            $variablesToReturn = [];
            foreach ($variables as $key => $value) {
                $variablesToReturn[$value] = $value;
            }
            return $variablesToReturn;
        } else {
            return null;
        }
    }

    /**
     * Create an associative array of the provenances objects indexed by their URI
     * @param type $provenances
     * @return array
     */
    private function mapProvenancesByUri($provenances) {
        $provenancesMap = [];
        if ($provenances !== null) {
            foreach ($provenances as $provenance) {
                $provenancesMap[$provenance->uri] = $provenance;
            }
        }

        return $provenancesMap;
    }

    /**
     * generate the csv file for the dataset creation action. The csv file is
     * generated with a column for each variable
     * @param array variables list of the variables to add to the 
     *                                file uri => alias
     * @return mixed
     */
    public function actionGenerateAndDownloadDatasetCreationFile() {
        $fileColumns[] = DatasetController::AGRONOMICAL_OBJECT_URI;
        $fileColumns[] = DatasetController::DATE;
        $variables = Yii::$app->request->post('variables');
        foreach ($variables as $variableAlias) {
            $fileColumns[] = $variableAlias;
        }

        $csvPath = "coma";
        if (Yii::$app->params['csvSeparator'] == ";") {
            $csvPath = "semicolon";
        }
        
        $file = fopen('./documents/DatasetFiles/' . $csvPath . '/datasetTemplate.csv', 'w');
        fputcsv($file, $fileColumns, $delimiter = Yii::$app->params['csvSeparator']);
        fclose($file);
    }
    
     /**
     * generate the csv file for the sensor dataset creation action. The csv file is
     * generated with a column for each variable
     * @param array variables list of the variables to add to the 
     *                                file uri => alias
     * @return mixed
     */
    public function actionGenerateAndDownloadSensorDatasetCreationFile() {
        $fileColumns[] = DatasetController::DATE;
        $variables = Yii::$app->request->post('variables');
        foreach ($variables as $variableAlias) {
            $fileColumns[] = $variableAlias;
        }
       
        $csvPath = "coma";
        if (Yii::$app->params['csvSeparator'] == ";") {
            $csvPath = "semicolon";
        }
        
        $file = fopen('./documents/DatasetFiles/' . $csvPath . '/datasetSensorTemplate.csv', 'w');
        fputcsv($file, $fileColumns, $delimiter = Yii::$app->params['csvSeparator']);
        fclose($file);
    }

  
    /**
     * 
     * @param array $csvErrors the errors founded. 
     * @param array $csvHeaders the header row of the csv file
     * @param array $correspondancesCSV the files columns corresponding to the wanted data
     * @return string the JavaScript code for the cells settings
     */
    private function getHandsontableCellsSettings($csvErrors, $csvHeaders, $correspondancesCSV) {
        $updateSettings = 'hot1.updateSettings({
                    cells: function(row, col, prop){
                       var cellProperties = {};
                       var cell = hot1.getCell(row,col);
                       cell.style.color = "black";';

        //2. Cells Errors    
        if (isset($csvErrors[DatasetController::ERRORS_ROWS])) {
            foreach ($csvErrors[DatasetController::ERRORS_ROWS] as $dataError) {
                $updateSettings .= 'if (row === ' . ($dataError[DatasetController::ERRORS_LINE] - 1)
                        . ' && col === ' . $dataError[DatasetController::ERRORS_COLUMN] . ') {'
                        . 'cell.style.fontWeight = "bold";'
                        . 'cell.style.color = "red";'
                        . '}';
            }
        }

        //3. Ignored columns
        foreach ($csvHeaders as $csvColumnNumber => $csvColumnName) {
            if (!in_array($csvColumnName, $correspondancesCSV)) {
                $updateSettings .= 'if (col === ' . $csvColumnNumber . ' ) { '
                        . 'cell.style.background = "#F2F2F2";'
                        . '}';
            }
        }

        $updateSettings .= 'return cellProperties;'
                . '}';

        //4. Missing Columns headers 
        if (isset($csvErrors[DatasetController::ERRORS_MISSING_COLUMN])) {
            $updateSettings .= ',afterGetColHeader: function (col, th) {';
            foreach ($csvErrors[DatasetController::ERRORS_MISSING_COLUMN] as $key => $missingColumn) {
                //$toAdd = 1 because missing columns keys begins at 0
                //         2 if there is also the errors rows columns 
                $updateSettings .= 'if (col === ' . ((count($csvHeaders) - 1) + $key + 1) . ' ) { '
                        . 'th.style.color = "red";'
                        . '}';
            }
            $updateSettings .= '}';
        }

        $updateSettings .= '});';
        return $updateSettings;
    }

    /**
     * generate an handsontable with the given data, each cell is readonly
     * @param array $arrayToDisplay in this array, the first row must be the 
     *                              headers
     * @return HandsontableSimple
     */
    private function generateHandsontableDataset($header, $arrayToDisplay) {
        $handsontable = new HandsontableSimple();

        //Columns headers an readonly
        //Add bold to headers
        $headerBold = null;
        foreach ($header as $column) {
            $headerBold[] = "<b>" . $column . "</b>";
        }

        $handsontable->setColHeaders($headerBold);
        $columnsDefinitions = [];
        foreach ($headerBold as $column) {
            $columnsDefinitions[] = new ColumnConfig([
                'readOnly' => true,
            ]);
        }
        $handsontable->setColumns($columnsDefinitions);

        //Add data
        $handsontable->setData($arrayToDisplay);

        return $handsontable;
    }

    /**
     * 
     * @param array $arrayData
     * @param array $arrayRowsErrors the array with the errors for each cell. 
     *                          Expected format : 
     *                           [0][ERRORS_LINE] = 0
     *                           [0][ERRORS_COLUMN] = 0
     *                           [0][ERRORS_MESSAGE] = error message
     * @return array the given $arrayData with a new column corresponding to the
     *               errors messages column
     */
    private function addColumnErrorToArray($arrayData, $arrayRowsErrors) {
        for ($i = 0; $i < count($arrayData); $i++) {
            $errorLine = "";
            if ($i === 0) {
                $errorLine = "Errors";
            } else {
                foreach ($arrayRowsErrors as $error) {
                    if ($error[DatasetController::ERRORS_LINE] === $i) {
                        $errorLine .= $error[DatasetController::ERRORS_MESSAGE] . " ";
                    }
                }
            }

            $arrayData[$i][] = $errorLine;
        }

        return $arrayData;
    }

    /**
     * 
     * @param array $arrayData
     * @param array $arrayMissingColumns the array with the list of the missing
     *                                   columns. Expected format : 
     *                                   [0] = AGRONOMICAL_OBJECT_URI
     * @return array the given $arrayData with news columns corresponding to the
     *               missing columns with empty values
     */
    private function addMissingColumnsToArray($arrayData, $arrayMissingColumns) {
        foreach ($arrayMissingColumns as $missingColumn) {
            for ($i = 0; $i < count($arrayData); $i++) {
                $cellValue = "Missing value";
                if ($i === 0) {
                    $cellValue = $missingColumn;
                }
                $arrayData[$i][] = $cellValue;
            }
        }
        return $arrayData;
    }

    /**
     * 
     * @param array $arrayData
     * @param array $arrayErrors the array with the errors. Expected format : 
     *                           [ERRORS_MISSING_COLUMNS][0] = AGRONOMICAL_OBJECT_URI
     *                           [...]
     *                           [ERRORS_MISSING_COLUMNS][3] = "Variable label"
     *                           [ERRORS_ROWS][0][ERRORS_LINE] = 0
     *                           [ERRORS_ROWS][0][ERRORS_COLUMN] = 0
     *                           [ERRORS_ROWS][0][ERRORS_MESSAGE] = error message
     * @return array the $arrayData with a new column corresponding to the 
     *               errors founded in the data and new columns corresponding to
     *               the missing columns if needed
     *               e.g. AgronomicalObjectUri, variableLabel, MessageError, MissingVariable, MissingDate
     */
    private function addColumnErrorsAndMissingToArray($arrayData, $arrayErrors) {

        //1. Add missing columns
        if (isset($arrayErrors[DatasetController::ERRORS_MISSING_COLUMN])) {
            $arrayData = $this->addMissingColumnsToArray($arrayData, $arrayErrors[DatasetController::ERRORS_MISSING_COLUMN]);
        }

        //2. add columnError
        if (isset($arrayErrors[DatasetController::ERRORS_ROWS])) {
            $arrayData = $this->addColumnErrorToArray($arrayData, $arrayErrors[DatasetController::ERRORS_ROWS]);
        }

        return $arrayData;
    }

    /**
     * register the dataset with the associated provenance and documents
     * @return mixed
     */
    public function actionCreate() {
        $datasetModel = new \app\models\yiiModels\YiiDatasetModel();
        $token = Yii::$app->session[WSConstants::ACCESS_TOKEN];

        // Load existing provenances
        $provenanceService = new WSProvenanceModel();
        $provenances = $this->mapProvenancesByUri($provenanceService->getAllProvenances($token));
        $this->view->params["provenances"] = $provenances;

         // Load existing agents
        $userModel = new \app\models\yiiModels\YiiUserModel();
        $users = $userModel->getPersonsURIAndName($token);
        $this->view->params['agents'] = $users;
        
        // Load experiments
        $experimentModel = new YiiExperimentModel();
        $experiments =  $experimentModel->getExperimentsURIAndLabelList($token);
        $this->view->params['experiments'] = $experiments;
        
        //If the form is complete, register data
        if ($datasetModel->load(Yii::$app->request->post())) {
            //Store uploaded CSV file
            $document = UploadedFile::getInstance($datasetModel, 'file');
            $serverFilePath = \config::path()['documentsUrl'] . "DatasetFiles/" . $document->name;
            $document->saveAs($serverFilePath);

            //Read CSV file content
            $fileContent = str_getcsv(file_get_contents($serverFilePath), "\n");
            $csvHeaders = str_getcsv(array_shift($fileContent), Yii::$app->params['csvSeparator']);
            unlink($serverFilePath);

            //Loaded given variables
            $experimentController = new ExperimentController();
            $experimentVariables = $experimentController->getExperimentMesuredVariablesSelectList($datasetModel->experiment) ;
            $csvRawVariables = array_slice($csvHeaders, 2);
            // clean variables name
            $csvVariables = array_map('trim', $csvRawVariables);
            // select all variables that don"t exist in experiment variables
            $variablesNotInExperiment = array_diff($csvVariables, array_values($experimentVariables)); 
            // Check CSV header with variables
            if (count($variablesNotInExperiment) === 0) {
                    $provenanceUri = $datasetModel->provenanceUri;
                    $SciencitificObjectSearch = new \app\models\yiiModels\ScientificObjectSearch();
                    $SciencitificObjectSearch->experiment = $datasetModel->experiment;
                    $SciencitificObjectSearch->pageSize = 30000;
                    $SciencitificObjectSearch->setWithProperties(false);
                    $SciencitificObjectSearchResults = $SciencitificObjectSearch->search($token);

                    $objectUris = [];
                    foreach ($SciencitificObjectSearchResults->getModels() as $object){
                        $objectUris[$object->uri]=$object->label;
                    }
                    $datasetModel->documentsURIs = null;
                        $objectsErrors = [];
                        // Save CSV data linked to provenance URI
                        $values = [];
                        foreach ($fileContent as $rowStr) {
                            $row = str_getcsv($rowStr, Yii::$app->params['csvSeparator']);
                            $scientifObjectAlias = $row[0];
                            if(!array_search($scientifObjectAlias, $objectUris)){
                                $objectsErrors[] = $scientifObjectAlias .  Yii::t("app/messages", " Object does not exists in this experiment");
                                $scientifObjectUri = null;
                            }else{
                                $scientifObjectUri = array_search($scientifObjectAlias, $objectUris);
                            }
                            $date = $row[1];
                            for ($i = 2; $i < count($row); $i++) {
                                $values[] = [
                                    "provenanceUri" => $provenanceUri,
                                    "objectUri" => $scientifObjectUri,
                                    "variableUri" => array_search($csvVariables[$i - 2], $experimentVariables),
                                    "date" => $date,
                                    "value" => $row[$i]
                                ];
                            }
                        }
                        
                        if(!empty($objectsErrors)){
                            return $this->render('create', [
                                'model' => $datasetModel,
                                'errors' => $objectsErrors
                                    ]
                            );
                        }
                       
                        
                        $dataService = new WSDataModel();
                        $result = $dataService->post($token, "/", $values);

                        // If data successfully saved
                        if (is_array($result->metadata->datafiles) && count($result->metadata->datafiles) > 0) {
                            $arrayData = $this->csvToArray($fileContent);
                            return $this->render('_form_dataset_created', [
                                        'model' => $datasetModel,
                                        'handsontable' => $this->generateHandsontableDataset($csvHeaders, $arrayData),
                                        'insertedDataNumber' => count($arrayData)
                            ]);
                        } else {

                            return $this->render('create', [
                                        'model' => $datasetModel,
                                        'errors' => $result->metadata->status
                            ]);
                        }
                
            } else {
                return $this->render('create', [
                            'model' => $datasetModel,
                            'errors' => [
                                Yii::t("app/messages", "CSV file headers does not match variables used in this experiment. The following Variables are not associated to this experiment " ) . "(" . implode(",", $variablesNotInExperiment) . ")"
                            ]
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $datasetModel,
            ]);
        }
    }
    
      /**
     * register the sensor data with the associated provenance and documents
     * @return mixed
     */
    public function actionCreateOnSensor() {
        $datasetModel = new \app\models\yiiModels\YiiDataSensorModel();
        $variablesModel = new \app\models\yiiModels\YiiVariableModel();

        $token = Yii::$app->session[WSConstants::ACCESS_TOKEN];

        // Load existing variables
        $variables = $variablesModel->getInstancesDefinitionsUrisAndLabel($token);
        $this->view->params["variables"] = $this->getVariablesListLabelToShowFromVariableList($variables);

        // Load existing provenances
        $provenanceService = new WSProvenanceModel();
        $provenances = $this->mapProvenancesByUri($provenanceService->getAllProvenances($token));
        $this->view->params["provenances"] = $provenances;
        
        // Load existing sensors
        $sensors = $this->getSensorsUrisTypesLabels($token);
        $this->view->params["sensingDevices"] = $this->getSensorListToShowFromSensorList($sensors);

         // Load existing agents
        $userModel = new \app\models\yiiModels\YiiUserModel();
        $users = $userModel->getPersonsMailsAndName($token);
        $this->view->params['agents'] = $users;
        //If the form is complete, register data
        if ($datasetModel->load(Yii::$app->request->post())) {
            //Store uploaded CSV file
            $document = UploadedFile::getInstance($datasetModel, 'file');
            $serverFilePath = \config::path()['documentsUrl'] . "DatasetFiles/" . $document->name;
            $document->saveAs($serverFilePath);

            //Read CSV file content
            $fileContent = str_getcsv(file_get_contents($serverFilePath), "\n");
            $csvHeaders = str_getcsv(array_shift($fileContent), Yii::$app->params['csvSeparator']);
            unlink($serverFilePath);

            $sensorController = new SensorController();
            $sensorVariables = $sensorController->getSensorMeasuredVariablesSelectList($datasetModel->provenanceSensingDevices) ;
            $csvVariables = array_slice($csvHeaders, 1);
            // select all variables that don"t exist in experiment variables
            $variablesNotInSensor = array_diff($csvVariables, array_values($sensorVariables)); 
     
            // Check CSV header with variables
            if (count($variablesNotInSensor) === 0) {
                // Get selected or create Provenance URI
               $provenanceUri = $datasetModel->provenanceUri;
                // Save CSV data linked to provenance URI
                $values = [];
                foreach ($fileContent as $rowStr) {
                    $row = str_getcsv($rowStr, Yii::$app->params['csvSeparator']);
                    $date = $row[0];
                    for ($i = 1; $i < count($row); $i++) {
                        $values[] = [
                            "provenanceUri" => $provenanceUri,
                            "variableUri" => array_search($csvVariables[$i - 1], $sensorVariables),
                            "date" => $date,
                            "value" => $row[$i]
                        ];
                    }
                }
                
                $dataService = new WSDataModel();
                $result = $dataService->post($token, "/", $values);
                // If data successfully saved
                if (is_array($result->metadata->datafiles) && count($result->metadata->datafiles) > 0) {
                    $arrayData = $this->csvToArray($fileContent);
                    return $this->render('_form_dataset_created', [
                                'model' => $datasetModel,
                                'handsontable' => $this->generateHandsontableDataset($csvHeaders, $arrayData),
                                'insertedDataNumber' => count($arrayData)
                    ]);
                } else {

                    return $this->render('create_on_sensor', [
                                'model' => $datasetModel,
                                'errors' => $result->metadata->status
                    ]);
                }
                    
                } else {
                    return $this->render('create_on_sensor', [
                                'model' => $datasetModel,
                                'errors' => [
                                    Yii::t("app/messages", "CSV file headers does not match variables followed by this sensor. The following Variables are not associated to this sensor " ) . "(" . implode(",", $variablesNotInSensor) . ")"
                                ]
                    ]);
            }
        } else {
            return $this->render('create_on_sensor', [
                        'model' => $datasetModel,
            ]);
        }
    }
    
    /**
     * Gets all sensors.
     * @return sensors 
     */
    public function getSensorsUrisTypesLabels() {
        $model = new \app\models\yiiModels\SensorSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $sensorsUrisTypesLabels = [];
        $sensors = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($sensors === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($sensors->models as $sensor) {
                $sensorsUrisTypesLabels[] =
                    [
                        self::SENSOR_DATA_URI => $sensor->uri,
                        self::SENSOR_DATA_LABEL => $sensor->label,
                        self::SENSOR_DATA_TYPE => $sensor->rdfType
                    ];
            }
        }
        return $sensorsUrisTypesLabels;
    }
    
    /**
     * 
     * @param type $sensorsUriTypesLabel
     * @return array
     */
    public function getSensorListToShowFromSensorList($sensorsUriTypesLabel) {
        $sensorLabelListToShow = [];
        foreach ($sensorsUriTypesLabel as $sensorUriTypesLabel) {
            $sensorType = Vocabulary::prettyUri($sensorUriTypesLabel[self::SENSOR_DATA_TYPE]);
            if (isset($sensorLabelListToShow[$sensorType])) {
                $sensorLabelListToShow[$sensorType][$sensorUriTypesLabel[self::SENSOR_DATA_URI]] = $sensorUriTypesLabel[self::SENSOR_DATA_LABEL];
            } else {
                $sensorLabelListToShow[$sensorType] = [
                $sensorUriTypesLabel[self::SENSOR_DATA_URI] => $sensorUriTypesLabel[self::SENSOR_DATA_LABEL]
                ];
            }
        }
        return $sensorLabelListToShow;
    }
}
