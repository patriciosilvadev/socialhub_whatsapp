<template>
    <div class="row">

        <div class="col-xl-4 col-lg-5 mt-2">
            <b-card class="" style="box-shadow:none">
                <div class="profile text-center ">
                    <h3>Meu perfil</h3><br>
                    <input id="fileUserPhoto" ref="fileUserPhoto" style="display:none"   type="file" @change.prevent="handleFileUserPhoto" accept="image/*"/>
                    <div class="container">
                        <a href="javascript:void()" @click.prevent="trigger()" style="outline:none !important;" alt="userLogged Image">
                            <img :src="userLogged.image_path" alt="Image profile" class="rounded-circle img-fluid profile-thumb">
                            <div class="overlay">                                
                                <div class="text"><i class="fa fa-camera" aria-hidden="true"></i><br>Click para atualizar</div>
                            </div>
                        </a>
                    </div>

                    <h4 class="text-gray">{{userLogged.name}}</h4>
                    <p>{{userLogged.role_name}}</p>                    
                </div>
                <div class="profile_details">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="nav-tabs-custom">
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        <div class="row">
                                            <div class="col-4 text-right mt-2">Email:</div>
                                            <div class="col-8 mt-2">{{userLogged.email}}</div>

                                            <div class="col-4 text-right mt-2">Telefone:</div>
                                            <div class="col-8 mt-2">{{userLogged.phone}}</div>

                                            <div class="col-4 text-right mt-2">C.P.F:</div>
                                            <div class="col-8 mt-2">{{userLogged.CPF}}</div>
                                            
                                            <div class="col-4 text-right mt-2">Whatsapp:</div>
                                            <div class="col-8 mt-2">{{userLogged.whatsapp_id}}</div>
                                            
                                            <div class="col-4 text-right mt-2">Facebook:</div>
                                            <div class="col-8 mt-2">{{userLogged.facebook_id}}</div>
                                            
                                            <div class="col-4 text-right mt-2">Instagram:</div>
                                            <div class="col-8 mt-2">{{userLogged.instagram_id}}</div>
                                            
                                            <div class="col-4 text-right mt-2">Linkedin:</div>
                                            <div class="col-8 mt-2">{{userLogged.linkedin}}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                            <div class="col-4 text-right mt-2">Criado em:</div>
                                            <div class="col-8 mt-2">{{userLogged.created_at}}</div>
                                            
                                            <div class="col-4 text-right mt-2">Atualizado em:</div>
                                            <div class="col-8 mt-2">{{userLogged.updated_at}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card>            
        </div>

        <div class="col-xl-8 col-lg-7 mt-2" >
            <b-card class="data" style="box-shadow:none">                
                <div title="Connections">
                    <h3>Atualizar meus dados:</h3><br>
                    <div>
                        <form>
                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Nome: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" v-model="user_edit.name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="text" readonly class="form-control" id="Email" v-model="user_edit.email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-sm-2 col-form-label">Senha</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" v-model="user_edit.password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="passwordRepeat" class="col-sm-2 col-form-label">Repetir senha:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="passwordRepeat" v-model="user_edit.repeat_password">
                                </div>
                            </div>                            
                            <div class="form-group row">
                                <label for="cpf" class="col-sm-2 col-form-label">CPF: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="cpf" v-model="user_edit.CPF">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phone" class="col-sm-2 col-form-label">Telefone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="phone" v-model="user_edit.phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="whatsapp_id" class="col-sm-2 col-form-label">Whatsapp</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="whatsapp_id" v-model="user_edit.whatsapp_id">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="whatsapp_id" class="col-sm-2 col-form-label">Facebook:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="whatsapp_id" v-model="user_edit.facebook_id">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="instagram_id" class="col-sm-2 col-form-label">Instagram:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="instagram_id" v-model="user_edit.instagram_id">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="linkedin_id" class="col-sm-2 col-form-label">Linkedin:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="linkedin_id" v-model="user_edit.linkedin_id">
                                </div>
                            </div>
                            <div class="form-group mt-5 text-right">
                                <button type="submit" class="btn btn-primary btn_width pl-5 pr-5" @click.prevent="updateUser">
                                    <i v-show="isSending==true" class="fa fa-spinner fa-spin" style="color:white" ></i> Atualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </b-card>
        </div>
    </div>
</template>
<script>
    import vScroll from "components/plugins/scroll/vScroll.vue"
    import ApiService from "resources/common/api.service";    
    import miniToastr from "mini-toastr";
    miniToastr.init();

    export default {
        name: "user_profile",

        components: {
            vScroll
        },

        data() {
            return {
                userLogged: {},
                url:'users',
                user_edit: null,
                isSending:false,
                file:null,
            }
        },


        methods:{

            updateUser(){
                if(!this.isSending && this.user_edit.password.trim() !='' && this.user_edit.password != this.user_edit.repeat_password){
                    miniToastr.error("As senhas fornecidas não coincidem. Por favor, confira!", "Erro");  
                    return;
                }else{
                    if(this.user_edit.password.trim() =='')
                        delete this.user_edit.password;
                    this.isSending = true;
                    ApiService.put(this.url+'/'+this.user_edit.id, this.user_edit)
                        .then(response => {
                            // window.location.reload(false);
                            this.userLogged = response.data;
                            window.localStorage.setItem('user', JSON.stringify(this.userLogged));
                            
                            this.user_edit = Object.assign({}, this.userLogged);
                            this.user_edit.repeat_password = this.user_edit.password = '';
                            miniToastr.success("Perfil atualizado com sucesso.","Sucesso");
                            this.isSending = false;
                        })
                        .catch(error => {
                            this.processMessageError(error, this.url, "update");
                            this.isSending = false;
                        });
                }
            },

            updateUserPhoto(This){
                if(!This.file){
                    return;
                }else{
                    let formData = new FormData();
                    formData.append("file",This.file);

                    ApiService.post(
                        This.url+'/'+This.user_edit.id+'/update_image',
                        formData,
                        {headers: { "Content-Type": "multipart/form-data" }}
                    )
                    .then(response => {
                        This.userLogged.image_path = response.data;
                        window.localStorage.setItem('user', JSON.stringify(This.userLogged));
                        
                        This.user_edit = Object.assign({}, This.userLogged);
                        This.user_edit.repeat_password = This.user_edit.password = '';
                        miniToastr.success("Foto atualizada com sucesso.","Sucesso");

                        window.location.reload(false);
                    })
                    .catch(error => {
                        this.processMessageError(error, This.url, "update_image");
                    });
                }
            },

            trigger () {
                this.$refs.fileUserPhoto.click();
            },

            handleFileUserPhoto: function() {
                this.file = null;
                if(this.$refs.fileUserPhoto.files[0].size < 4*1024*1024) {
                    this.file = this.$refs.fileUserPhoto.files[0];
                    this.updateUserPhoto(this);
                } else{
                    miniToastr.error("A imagem deve ter tamanho inferior a 4MB", "Erro"); 
                }
            },

            //------ Specific exceptions methods------------
            processMessageError: function(error, url, action) {
                var info = ApiService.process_request_error(error, url, action);
                if(info.typeException == "expiredSection"){
                    miniToastr.warn(info.message,"Atenção");
                    this.$router.push({name:'login'});
                    window.location.reload(false);
                }else if(info.typeMessage == "warn"){
                    miniToastr.warn(info.message,"Atenção");
                }else{
                    miniToastr.error(info.erro, info.message); 
                }
            },

        },


        beforeMount: function () {
            this.userLogged = JSON.parse(window.localStorage.getItem('user'));
            switch(this.userLogged.role_id){
                case 1:
                    this.userLogged.role_name = "Administrador de SocialHub";
                    break;
                case 2:
                    this.userLogged.role_name = "Funcionário de SocialHub";
                    break;
                case 3:
                    this.userLogged.role_name = "Gerente de empresa";
                    break;
                case 4:
                    this.userLogged.role_name = "Funcionário Atendente";
                    break;
                case 5:
                    this.userLogged.role_name = "Convidado";
                    break;
            }
            this.user_edit = Object.assign({}, this.userLogged);
            this.user_edit.repeat_password = this.user_edit.password = '';
            console.log( this.userLogged);
        },

        created() {
            miniToastr.setIcon("error", "i", {class: "fa fa-times"});
            miniToastr.setIcon("warn", "i", {class: "fa fa-exclamation-triangle"});
            miniToastr.setIcon("info", "i", {class: "fa fa-info-circle"});
            miniToastr.setIcon("success", "i", {class: "fa fa-arrow-circle-o-down"});
        },

        mounted: function () {
        },

        destroyed: function () {
        }
    }
</script>
<style src="../../../css/user_profile.css" scoped></style>

<style lang="scss" scoped>
    .text {
        color: white;
        font-size: 1.4em;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .container {
        position: relative;
        width: 200px;
        height: 200px;
    }

    .overlay {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        // height: 100%;
        // width: 100%;
        width: 200px;
        height: 150px;
        opacity: 0;
        transition: .5s ease;
        background-color: rgb(11, 12, 12);        
    }

    .container:hover .overlay {
        opacity: 0.6;
    }

    
</style>