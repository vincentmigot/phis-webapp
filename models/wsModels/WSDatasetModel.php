<?php

//**********************************************************************************************
//                                       WSDatasetModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 2 2017
// Subject: Corresponds to the phenotypes service. extends WSModel
//***********************************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the dataset service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDatasetModel extends \openSILEX\guzzleClientPHP\WSModel {
   
    /**
     * initialize access to the dataset service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "datasets");
    }
}

