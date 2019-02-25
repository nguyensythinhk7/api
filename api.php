<?php
    /**
     * Created by PhpStorm.
     * User: phich
     * Date: 2/20/19
     * Time: 9:49 AM
     */

    include(__DIR__."/model/imageResourcesDAO.php");
    $viva = new imageResources();
    $_operation = $_REQUEST['_operation'];

    switch ($_operation) {
        case 'getImages':
                $viva->getAllImageResource();
            break;
        case 'addImages':
            $viva->postImageUrl($_REQUEST["url"]);
            break;
        case 'uploadImage':
            $viva->uploadImage();
            break;
        default:
            $viva->errorOperation('Not Found Operation !');

    }