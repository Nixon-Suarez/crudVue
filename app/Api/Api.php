<?php
require_once "../../config/app.php";
require_once "../../autoload.php";
use app\controllers\userController;

$response['success'] = false;
if(isset($_REQUEST['opc'])){
    $opc = $_REQUEST['opc'];
    $insUser = new userController();
    switch($opc){
        case 'list':
            $res = $insUser->getUserList();
            if($res['respuesta']){
                $response['success'] = true;
                $response['users'] = $res['respuesta'];
            }
            $response['alert'] = $res['alerta'];
            break;
        case 'update':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $file =  isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
            $res = $insUser->updateUser($id,$name, $email, $file);
            if($res['respuesta']){
                $response['success'] = true;
            }
            $response['alert'] = $res['alerta'];
            break;
        case 'create':
            $name = $_POST['name'];
            $email = $_POST['email'];
            $file = $_FILES['file']['name'];
            $res = $insUser->createUser($name, $email, $file);
            if($res['respuesta']){
                $response['success'] = true;
            }
            $response['alert'] = $res['alerta'];
            break;
        case 'delete':
            $id = $_POST['id'];
            $res = $insUser->deleteUser($id);
            if($res['respuesta']){
                $response['success'] = true;
            }
            $response['alert'] = $res['alerta'];
            break;
    }
}
die(json_encode($response));