<?php
require_once "./config/app.php";
require_once "autoload.php";
?>
<!DOCTYPE html>
<html lang="en">
    <?php require_once "./app/inc/head.php";?>
<body>
    <div id="app" class="container mb-4 content">
        <button @click="modalCreateUser=true" class="btn btn-success">Crear</button>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-hover">
                <thead class="custom-header text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center" v-for="user in users" :key="user.id">
                        <td>{{user.id}}</td>
                        <td>{{user.name}}</td>
                        <td>{{user.email}}</td>
                        <td class="text-center">
                            <!-- ajustar -->
                            <img :src ="URL_IMGs + user.imagen" width="200" alt="">
                        </td>
                        <td class="d-flex justify-content-center gap-2">
                            <button @click="modalUpdateUser=true; selectUser(user)" class="btn btn-primary">
                                <i class="bi bi-eye"></i> Editar
                            </button>
                            <button @click="deleteUser(user.id)" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- CREATE -->
        <div v-if="modalCreateUser" class="modal-backdrop fade show"></div>
        <div v-if="modalCreateUser" class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Usuario</h5>
                        <button type="button" class="btn-close" @click="modalCreateUser = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="newUser">
                            <!-- name -->
                            <div class="mb-3">
                                <label for="nombreUsuario" class="form-label">Nombres</label>
                                <label for="nombreUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input type="text" class="form-control"
                                    id="nombreUsuario" name="nombreUsuario" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="60" required>
                                </input>
                            </div>
                            <!-- email -->
                            <div class="mb-3">
                                <label for="emailUsuario" class="form-label">Email</label>
                                <label for="emailUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input type="email" class="form-control"
                                    id="emailUsuario" name="emailUsuario" maxlength="70" required>
                                </input>
                            </div>
                            <!-- imagen -->
                            <div class="mb-3 text-center">
                                <label for="imagenUsuario" class="form-label">Seleccione un archivo</label>
                                <label for="imagenUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input class="form-control" type="file" id="imagenUsuario" name="imagenUsuario" accept=".jpg,.jpeg,.png" required>
                                <div class="form-text">JPG, JPEG, PNG. (MAX 10MB)</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    Registrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- UPDATE -->
        <div v-if="modalUpdateUser" class="modal-backdrop fade show"></div>
        <div v-if="modalUpdateUser" class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Actualizar Usuario</h5>
                        <button type="button" class="btn-close" @click="modalUpdateUser = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="updateUser">
                            <!-- name -->
                            <div class="mb-3">
                                <label for="nombreUsuario" class="form-label">Nombres</label>
                                <label for="nombreUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input type="text" class="form-control"
                                    id="nombreUsuario" name="nombreUsuario" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" v-model="currentUser.name" maxlength="60" required>
                                </input>
                            </div>
                            <!-- email -->
                            <div class="mb-3">
                                <label for="emailUsuario" class="form-label">Email</label>
                                <label for="emailUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input type="email" class="form-control"
                                    id="emailUsuario" name="emailUsuario" maxlength="70" v-model="currentUser.email"required>
                                </input>
                            </div>
                            <!-- imagen -->
                            <div v-if="currentUser.imagen" class="mb-3 text-center">
                                <a
                                    :href="URL_IMGs + currentUser.imagen"
                                    class="btn btn-outline-primary btn-sm"
                                    download>
                                    <i class="bi bi-download"></i> Descargar archivo
                                </a>
                            </div>
                            <div class="mb-3 text-center">
                                <label for="imagenUsuario" class="form-label">Seleccione un archivo</label>
                                <label for="imagenUsuario" class="form-label asterisco-obligatorio">*</label>
                                <input class="form-control" type="file" id="imagenUsuario" name="imagenUsuario" accept=".jpg,.jpeg,.png">
                                <div class="form-text">JPG, JPEG, PNG. (MAX 10MB)</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo APP_URL;?>app/js/app.js"></script>
</body>
</html>