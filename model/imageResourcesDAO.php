<?php

include(__DIR__."/../config/systemConfig.php");

class imageResources {
    private $dbReference;
    var $dbConnect;
    var $result;

    /**
     * db_api
     * table images(id, url)
     */

    function __construct(){
        $this->dbReference = new systemConfig();
        $this->dbConnect = $this->dbReference->connectDB();
    }

    function __destruct(){
        $this->dbConnect->close();
    }

    //get images
    function getAllImageResource(){
        if ($this->dbConnect == NULL) {
            $this->dbReference->sendResponse(503,'{"error_message":'.$this->dbReference->getStatusCodeMeeage(503).'}');
        }else{
            $sql = "SELECT * FROM images ";
            if(isset($_REQUEST["number_per_page"]) && isset($_REQUEST["page"])){
                $number_per_page = $_REQUEST["number_per_page"];
                $page = ($_REQUEST["page"]-1)*$number_per_page +1;
                $page_next = $_REQUEST["page"]*$number_per_page;
                if ($page != NULL && $number_per_page != NULL) {
                    $sql = "SELECT * FROM images WHERE id BETWEEN $page AND $page_next";
                    $this->queryData($sql);
                }
            }else if($_REQUEST["id"]){
                $sql .= "WHERE id = ".$_REQUEST["id"];
                $this->queryData($sql);

            }else{
                $this->dbReference->error('Param Failed!');
            }
        }
    }

    function postImageUrl($url){
        if ($this->dbConnect == NULL) {
            $this->dbReference->sendResponse(503,'{"error_message":'.$this->dbReference->getStatusCodeMeeage(503).'}');
        }else{
            if(isset($url)){
                $sql = 'INSERT INTO `images`(`url`) VALUES ("'.$url.'")';
                $this->result = $this->dbConnect->query($sql);
                if($this->result == 1){
                    $this->dbReference->success('"success!"', 201);
                }else{
                    $this->dbReference->error('Post image failed!');
                }
            }else{
                $this->dbReference->error('Param Failed!');
            }
        }
    }

    function errorOperation($message){
        $this->dbReference->error($message);
    }

    function queryData($sql) {
        echo $sql;
        $this->result = $this->dbConnect->query($sql);
        if($this->result->num_rows > 0){
            // output data of each row
            $resultSet = array();
            while($row = $this->result->fetch_assoc()) {
                $resultSet[] = $row;
            }
            $this->dbReference->success(json_encode($resultSet));
        }else{
            $this->dbReference->success('"null"');
        }
    }

    /**
     *
     */
    function uploadImage(){
        // Đường dẫn của file
        $dir_upload = "uploads/";

        // info của file
        $file_name = $_FILES["image"]["name"];
        $file_size = $_FILES["image"]["size"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $mutil_image = count($file_name);
        $message = [];
        //upload mutil image
        if($mutil_image > 1){
            for($i = 0; $i < $mutil_image; $i++){
                $target_file = $dir_upload. $file_name[$i];
                //Type của File
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                //File image
                $this->checkTypeImage($file_tmp[$i], $imageFileType);
                $new_target_file = $this->checkExistReturnNewTargetFile($target_file, $imageFileType);
                $this->dbReference->error($new_target_file);
                $this->checkImageSize($file_size[$i], 3);

                if($this->moveImageFile($file_tmp[$i], $new_target_file)){
                    $message[$i] = $this->dbReference->getWebsite() . $new_target_file;
                }else{
                    $this->dbReference->error("Error when upload image !");
                    exit;
                }
            }
            $this->dbReference->success(json_encode($message));
        }else if($mutil_image == 1){
            if(gettype($file_name) == "array"){
                // upload one image of mutil
                $file_name = $file_name[0];
                $file_tmp = $file_tmp[0];
                $file_size = $file_size[0];
            }
            $target_file = $dir_upload . basename($file_name);
            //Type của File
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            //File image
            $this->checkTypeImage($file_tmp, $imageFileType);
            $new_target_file = $this->checkExistReturnNewTargetFile($target_file, $imageFileType);
            $this->checkImageSize($file_size, 3);
            if($this->moveImageFile($file_tmp, $new_target_file)){
                $this->dbReference->success('{"url": "'.$this->dbReference->getWebsite() . $new_target_file .'"}');
            }
        }else{
            $this->dbReference->error('No image selected !');
        }
    }

    function checkTypeImage($file_tmp, $imageFileType){
        // Check if image file is a actual image or fake image
        $check = getimagesize($file_tmp);
        if($check == false) {
            $this->dbReference->error( "File is not an image.");
            exit;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $this->dbReference->error('Sorry, only JPG, JPEG, PNG & GIF files are allowed');
            exit;
        }
    }
    function checkExistReturnNewTargetFile($target_file, $imageFileType){
        // Check if file already exists rename
        if (file_exists($target_file) == 1) {
            //rename image
            $filename_notype = explode('.'.$imageFileType, $target_file);
            $i = 1;
            while(file_exists($target_file)){
                ++$i;
                $target_file = $filename_notype[0].'_'.$i.'.'.$imageFileType;
            }
            return $target_file;
        } else{
            return $target_file;
        }
    }
    function checkImageSize($file_size, $max_size = 2){
        // Check file size
        if ($file_size > ($max_size * 1024 * 1024)) {
            $this->dbReference->error('Sorry, your file is too large. Max size: '.$max_size.'MB' );
            exit;
        }
    }
    function moveImageFile($file_tmp, $target_file){
        if (move_uploaded_file($file_tmp, $target_file)) {
            return true;
        } else {
            return false;
        }
    }
}

?>