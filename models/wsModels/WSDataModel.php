<?php

//******************************************************************************
//                                       WSDataModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the data service
 * @see \openSILEX\guzzleClientPHP\WSModel 
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDataModel extends\openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the projects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "data");
    }
    
    public function getAllData($sessionToken, $params) {
        $params[WSConstants::PAGE] = 0;
        $params[WSConstants::PAGE_SIZE] = 1000;
        $params["dateSortAsc"] = "true";
        $dataResult = $this->get($sessionToken, "", $params);
        
        $result = [];
        if (isset($dataResult->{WSConstants::RESULT}->{WSConstants::DATA})) {
            $result = $dataResult->{WSConstants::RESULT}->{WSConstants::DATA};
        }
        
        if (isset($dataResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}) && isset($dataResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT}) && $dataResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT} > 0) {

            $totalPages = $dataResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_PAGES};

            for ($currentPage = 1; $currentPage < $totalPages; $currentPage++) {
                $params[WSConstants::PAGE] = $currentPage;
                $dataResult = $this->get($sessionToken, "", $params);
                
                $result = array_merge($result, $dataResult->{WSConstants::RESULT}->{WSConstants::DATA});
            }
            
        }
        
        return $result;
    }
}