const api = "http://localhost/php/index.php/CRUD_Vue/app/Api/api.php"
const app = Vue.createApp({
    data(){ 
        return{
            // message : "Holaa",
            URL_IMGs : 'http://localhost/php/index.php/CRUD_Vue/app/img/uploads/usuarios/',
            modalCreateUser : false,
            modalUpdateUser : false,
            users : [],
            currentUser : {}
        }
    },
    mounted(){
        this.getUsers()
    },
    methods:{
        getUsers(){
            axios.get(api+"?opc=list")
            .then(function(response){
                // console.log(response.data.users)
                app.users = response.data.users
            })
            .catch(function(error){
                console.error("Error en la solicitud:", error);
                Swal.fire(
                    "Error",
                    "Ocurrió un error en el servidor",
                    "error"
                );
            })
        },
        newUser(){
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se enviará toda la información",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (!result.isConfirmed) return;
                let fd = new FormData()
                fd.append("name", document.getElementById("nombreUsuario").value)
                fd.append("email", document.getElementById("emailUsuario").value)
                fd.append("file", document.getElementById("imagenUsuario").files[0])

                axios.post(api+"?opc=create", fd)
                .then(function(response){
                    console.log(response.data)
                    app.getUsers()
                    alertas_ajax(response.data.alert);
                })
                .catch(function(error){
                    console.error("Error en la solicitud:", error);
                    Swal.fire(
                        "Error",
                        "Ocurrió un error en el servidor",
                        "error"
                    );
                })
            });
        },
        updateUser(){
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se enviará toda la información",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (!result.isConfirmed) return;
                let fd = new FormData()
                fd.append("id", app.currentUser.id)
                fd.append("name", document.getElementById("nombreUsuario").value)
                fd.append("email", document.getElementById("emailUsuario").value)
                fd.append("file", document.getElementById("imagenUsuario").files[0])

                axios.post(api+"?opc=update", fd)
                .then(function(response){
                    console.log(response.data)
                    app.currentUser = {}
                    app.getUsers()
                    alertas_ajax(response.data.alert);
                })
                .catch(function(error){
                    console.error("Error en la solicitud:", error);
                    Swal.fire(
                        "Error",
                        "Ocurrió un error en el servidor",
                        "error"
                    );
                })
            })
        },
        deleteUser(id){
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se enviará toda la información",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (!result.isConfirmed) return;
                let fd = new FormData()
                fd.append("id", id)

                axios.post(api+"?opc=delete", fd)
                .then(function(response){
                    console.log(response.data)
                    app.getUsers()
                    alertas_ajax(response.data.alert);
                })
                .catch(function(error){
                    console.error("Error en la solicitud:", error);
                    Swal.fire(
                        "Error",
                        "Ocurrió un error en el servidor",
                        "error"
                    );
                })
            })
        },
        searchUser(){
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se enviará toda la información",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (!result.isConfirmed) return;
                let fd = new FormData()
                fd.append("name_filtro", document.getElementById("nombre").value)
                fd.append("email_filtro", document.getElementById("email").value)
                axios.post(api+"?opc=list", fd)
                .then(function(response){
                    console.log(response.data)
                    app.users = response.data.users
                    alertas_ajax(response.data.alert);
                })
                .catch(function(error){
                    console.error("Error en la solicitud:", error);
                    Swal.fire(
                        "Error",
                        "Ocurrió un error en el servidor",
                        "error"
                    );
                })
            })
        },
        selectUser(user){
            app.currentUser = user
        }
    }
}).mount("#app")

function alertas_ajax(alerta){
    if(alerta.tipo=="simple"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            theme: "light",
            confirmButtonText: "Aceptar"
        });
    }else if(alerta.tipo=="recargar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            theme: "light",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed) {
                location.reload(); // recarga la pagina
            }
        });
    }else if(alerta.tipo=="limpiar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            theme: "light",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed) {
                let formulario = document.querySelector(".FormularioAjax"); //selecciona el primer formulario con la clase FormularioAjax
                formulario.reset(); //limpia el formulario
            }
        });
    }else if(alerta.tipo=="redireccionar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            theme: "light",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed) {
                window.location.href = alerta.url; // redirecciona a la url que se le pasa
            }
        });
    }
}