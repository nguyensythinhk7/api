<?php
    /**
     * Created by PhpStorm.
     * User: phich
     * Date: 2/20/19
     * Time: 9:49 AM
     */
    $_key_sever = "dkd4s5ds5ds4255s4d525e2";
    include(__DIR__."/model/imageResourcesDAO.php");
    $viva = new imageResources();
    $_operation = $_REQUEST['_operation'];
    $_key = $_REQUEST["key"];

    if($_key_sever === $_key){
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
    } else {
        $viva->errorKey('you not access use api!');
    }

