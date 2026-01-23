<?php
namespace app\controllers;

use app\models\mainModel;
use app\models\eloquent\User;

class userController extends mainModel{
    public function getUserList($pagina = 1, $registros = 10, $email = "", $name ="")
    {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $email = trim($this->limpiarCadena($email ?? ''));
        $name = $this->limpiarCadena($name) ?? '';
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $registros = ($registros > 0) ? (int)$registros : 10;
        $inicio = ($pagina > 0) ? (($registros * $pagina) - $registros) : 0;
        // consulta
        $query = User::query();
        // Filtro por búsqueda
        if (!empty($email)) {
            $query->where("email", 'LIKE', "%$email%");
        }
        if (!empty($name)) {
            $query->where("name", 'LIKE', "%$name%");
        }
        // $consulta_total = (clone $query)->count();

        $consulta_datos = $query->orderBy('id', 'DESC')
            ->skip($inicio)
            ->take($registros)
            ->get();

        $consulta_datos = $consulta_datos->isNotEmpty() ? $consulta_datos : false;
        $alerta = "exitos";

        return ["respuesta" => $consulta_datos, "alerta" => $alerta]; 
    }
    public function createUser($name, $email, $file)
    {
        $name = trim($this->limpiarCadena($name ?? ''));
        $email = trim($this->limpiarCadena($email ?? ''));
        $file = $file ?? '';

        // verificar campos obligatorios
        $campos = [
            $name,
            $email
        ];
        if (in_array("", $campos, true)) {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No has llenado todos los campos obligatorios",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        #Verificando integridad de los datos
        if ($this->verificarDatos("^(?!\s*$)[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]{1,150}$", $name)) {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Ocurrio un error inesperado",
                "texto" => "El nombre del usuario no coincide con el formato solicitado",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        if(filter_var( $email, FILTER_VALIDATE_EMAIL)){ # verifica si el email es valido
            $check_email = User::where("email", $email)->first();
            if($check_email){
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El email no es valido",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
        }else{
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Ocurrio un error inesperado",
                "texto" => "El email no es valido",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        $doc_dir = "../img/uploads/usuarios/";
        if ($file != "" && $_FILES['file']['size'] > 0) {
            //  creando directorio si no existe
            if (!file_exists($doc_dir)) {
                if (!mkdir($doc_dir, 0777)) {
                    $alerta = json_encode([
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "No se pudo crear el directorio",
                        "icono" => "error"
                    ]);
                    return ["respuesta" => false, "alerta" => $alerta];
                }
            }
            // limitar que tipo de archivo
            $mimePermitidos = [
                'image/jpeg',
                'image/png',
                'image/jpg'
            ];

            $mimeArchivo = mime_content_type($_FILES['file']['tmp_name']);

            if (!in_array($mimeArchivo, $mimePermitidos)) {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Archivo no permitido, solo se permiten archivos .jpg, .jpeg, .png",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            # limitar el peso del archivo
            if (($_FILES['file']['size'] / 1024) > 10000) { // 10MB
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El archivo no puede ser mayor a 10MB",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            #Extencion del archivo
            switch ($mimeArchivo) {
                case 'image/jpeg':
                    $extension = '.jpeg';
                    break;
                case 'image/png':
                    $extension = '.png';
                    break;
                case 'image/jpg':
                    $extension = '.jpg';
                    break;
                default:
                    $extension = '.png';
            }

            chmod($doc_dir, 0777);

            // renombra la archivo_usuario
            $nombreLimpio = str_ireplace(" ", "_", pathinfo($file, PATHINFO_FILENAME));
            $archivo_usuario = $nombreLimpio . "_" . rand(1000, 9999) . "_" . time() . $extension;

            // mover la img al directorio de imagenes
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $doc_dir . $archivo_usuario)) {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "Error al subir el archivo, intente nuevamente",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
        } else {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Ocurrio un error inesperado",
                "texto" => "Debe seleccionar un adjunto",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        $datos_usuario_reg = [
            "name" => $name,
            "email" => $email,
            "imagen" => $archivo_usuario
        ];
        try {
            $nuevo_usuario = User::create($datos_usuario_reg);
            if ($nuevo_usuario) {
                $alerta = json_encode([
                    "tipo" => "recargar",
                    "titulo" => "Usuario creado",
                    "texto" => "Usuario ha sido añadido exitosamente",
                    "icono" => "success",
                ]);
                return ["respuesta" => true, "alerta" => $alerta];
            } else {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Error",
                    "texto" => "No se pudo añadir al usuario, por favor intente nuevamente",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta,];
            }
        } catch (\Exception $e) {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Ocurrió un error al procesar la solicitud: " . $e->getMessage(),
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
    }
    public function updateUser($id, $name, $email, $file){
        $id = $this->limpiarCadena($id ?? '');
        $name = trim($this->limpiarCadena($name ?? ''));
        $email = trim($this->limpiarCadena($email ?? ''));
        $file = $file ?? '';

        // verificar campos obligatorios
        $campos = [
            $name,
            $email,
            $id
        ];
        if (in_array("", $campos, true)) {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No has llenado todos los campos obligatorios",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        // verifica que el usuario exista
        $check_user = User::where("id", $id)->first();
        if(!$check_user){
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Ocurrio un error inesperado",
                "texto" => "El usuario no existe",
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
        $current_id = $check_user->id;
        $current_email = $check_user->email;
        $current_name = $check_user->name;
        $current_file = $check_user->imagen;

        if ($name != $current_name){
            #Verificando integridad de los datos
            if ($this->verificarDatos("^(?!\s*$)[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]{1,150}$", $name)) {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El nombre del usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
        }
        if($email != $current_email){
            if(filter_var( $email, FILTER_VALIDATE_EMAIL)){ # verifica si el email es valido
                $check_email = User::where("email", $email)
                    ->where("id", "!=", $id)
                    ->first();
                if($check_email){
                    $alerta = json_encode([
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "El email ya está registrado",
                        "icono" => "error"
                    ]);
                    return ["respuesta" => false, "alerta" => $alerta];
                }
            }else{
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El email no es valido",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
        }
        $doc_dir = "../img/uploads/usuarios/";
        if ($file != "" && $_FILES['file']['size'] > 0) {
            //  creando directorio si no existe
            if (!file_exists($doc_dir)) {
                if (!mkdir($doc_dir, 0777)) {
                    $alerta = json_encode([
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "No se pudo crear el directorio",
                        "icono" => "error"
                    ]);
                    return ["respuesta" => false, "alerta" => $alerta];
                }
            }
            // limitar que tipo de archivo
            $mimePermitidos = [
                'image/jpeg',
                'image/png',
                'image/jpg'
            ];

            $mimeArchivo = mime_content_type($_FILES['file']['tmp_name']);

            if (!in_array($mimeArchivo, $mimePermitidos)) {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Archivo no permitido, solo se permiten archivos .jpg, .jpeg, .png",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            # limitar el peso del archivo
            if (($_FILES['file']['size'] / 1024) > 10000) { // 10MB
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El archivo no puede ser mayor a 10MB",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            #Extencion del archivo
            switch ($mimeArchivo) {
                case 'image/jpeg':
                    $extension = '.jpeg';
                    break;
                case 'image/png':
                    $extension = '.png';
                    break;
                case 'image/jpg':
                    $extension = '.jpg';
                    break;
                default:
                    $extension = '.png';
            }

            chmod($doc_dir, 0777);

            // renombra la archivo_usuario
            $nombreLimpio = str_ireplace(" ", "_", pathinfo($file, PATHINFO_FILENAME));
            $archivo_usuario = $nombreLimpio . "_" . rand(1000, 9999) . "_" . time() . $extension;

            // mover la img al directorio de imagenes
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $doc_dir . $archivo_usuario)) {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "Error al subir el archivo, intente nuevamente",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            if (is_file($doc_dir . $check_user->imagen)) { #valida si la img existe en el directorio
                chmod($doc_dir . $check_user->imagen, 0777);
                unlink($doc_dir . $check_user->imagen); #si existe la elimina
            }
        } else {
            $archivo_usuario = $current_file;
        }
        $datos_usuario_reg = [
            "name" => $name,
            "email" => $email,
            "imagen" => $archivo_usuario
        ];
        try {
            $actualizacion_usuario = User::where("id",$current_id)
                ->update($datos_usuario_reg);
            if ($actualizacion_usuario){
                $alerta = json_encode([
                    "tipo" => "recargar",
                    "titulo" => "Usuario actualizado",
                    "texto" => "Usuario ha sido actualizado exitosamente",
                    "icono" => "success",
                ]);
                return ["respuesta" => true, "alerta" => $alerta];
            } else {
                $alerta = json_encode([
                    "tipo" => "simple",
                    "titulo" => "Error",
                    "texto" => "No se pudo actualizar al usuario, por favor intente nuevamente",
                    "icono" => "error"
                ]);
                return ["respuesta" => false, "alerta" => $alerta,];
            }
        } catch (\Exception $e) {
            $alerta = json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Ocurrió un error al procesar la solicitud: " . $e->getMessage(),
                "icono" => "error"
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
    }
    public function deleteUser($id){
        $id = trim($this->limpiarCadena($id ?? ''));
        try{
            if (!is_numeric($id) || $id <= 0) {
                $alerta = json_encode([
                    'tipo' => 'simple',
                    'titulo' => 'Error',
                    'texto' => 'ID de usuario no válido',
                    'icono' => 'error'
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            $check_user = User::where("id", $id)->first();
            if(!$check_user){
                $alerta = json_encode([
                    'tipo' => 'simple',
                    'titulo' => 'No encontrado',
                    'texto' => 'El usuario no existe',
                    'icono' => 'error'
                ]);
                return ["respuesta" => false, "alerta" => $alerta];
            }
            $doc_dir = "../img/uploads/usuarios/";
            $delete_user = User::destroy($id);
            if ($delete_user) {
                if (is_file($doc_dir . $check_user->imagen)) {
                    chmod($doc_dir . $check_user->imagen, 0777);
                    unlink($doc_dir . $check_user->imagen);
                }
                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario eliminado",
                    "texto" => "El usuario " . $check_user->name . " ha sido eliminado del sistema correctamente",
                    "icono" => "success"
                ];
                return ["respuesta" => true, "alerta" => $alerta];
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No hemos podido eliminar el usuario " . $check_user->name . " del sistema, por favor intente nuevamente",
                    "icono" => "error"
                ];
                return ["respuesta" => false, "alerta" => $alerta];
            }
        }catch (\Exception $e) {
            error_log("Error en eliminargetOfertaDocumentControlar: " . $e->getMessage());
            $alerta = json_encode([
                'tipo' => 'simple',
                'titulo' => 'Error',
                'texto' => 'Error al cargar la oferta',
                'icono' => 'error'
            ]);
            return ["respuesta" => false, "alerta" => $alerta];
        }
    }
}