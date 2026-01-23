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
            })
        },
        newUser(){
            let fd = new FormData()
            fd.append("name", document.getElementById("nombreUsuario").value)
            fd.append("email", document.getElementById("emailUsuario").value)
            fd.append("file", document.getElementById("imagenUsuario").files[0])

            axios.post(api+"?opc=create", fd)
            .then(function(response){
                console.log(response.data)
                app.getUsers()
            })
            .catch(function(error){
                console.error("Error en la solicitud:", error);
            })
        },
        updateUser(){
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
            })
            .catch(function(error){
                console.error("Error en la solicitud:", error);
            })
        },
        deleteUser(id){
            let fd = new FormData()
            fd.append("id", id)

            axios.post(api+"?opc=delete", fd)
            .then(function(response){
                console.log(response.data)
                app.getUsers()
            })
            .catch(function(error){
                console.error("Error en la solicitud:", error);
            })
        },
        selectUser(user){
            app.currentUser = user
        }
    }
}).mount("#app")