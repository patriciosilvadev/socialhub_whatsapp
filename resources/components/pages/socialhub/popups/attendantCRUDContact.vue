<template>
    <div class="col-12">
        <form v-show="action=='insert' || action=='edit'">   
            <div class="col-lg-12 sect_header">
                <ul v-if='action=="insert"' class="menu">
                    <li ><a  href="javascript:void(0)" @click.prevent="toggle_left('close')"><i class="fa fa-arrow-left" aria-hidden="true"></i></a></li>
                    <li ><p class="header-title">Novo contato</p> </li>                        
                    <ul class="menu float-right">
                        <li ><a href="javascript:void(0)" @click.prevent="toggle_left('close')"><i class="fa fa-close"></i></a></li>
                    </ul>
                </ul>
                <ul v-if='action=="edit"' class="menu">
                    <li ><a href="javascript:void(0)" @click.prevent="editclose"><i class="fa fa-close"></i></a></li>
                    <li ><p class="header-title">Editar contato</p> </li>
                    <ul class="menu float-right">
                        <li ><a  href="javascript:void(0)" @click.prevent="editclose"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></li>
                    </ul>
                </ul>
            </div>
            <form class="col-lg-12 form-horizontal form-validation" :state="formstate" style="background-color:white">
                
                <br><br>
                <div class="col-lg-12 input-group">
                    <div class="input-group-prepend">
                        <span class="fa fa-whatsapp input-group-text text-muted border-right-0 pt-2 outline" required placeholder="WhatsApp (*)" style="background-color:white"></span>
                    </div>
                    <input type="text" v-model="model.whatsapp_id" v-mask="'###############'" title="Ex: 5511988888888" class="form-control border-left-0 outline" placeholder="Whatsapp(*)" >
                    <div class="input-group-append" title="Conferir número">
                        <button class="btn btn-info input-group-text text-muted border-right-0 pt-2 outline" @click.prevent="checkWhatsappNumber">
                            <span v-if="!isCheckingWhatsapp" class="fa fa-check"></span>
                            <span v-if="isCheckingWhatsapp" class="fa fa-spinner fa-spin "></span>
                        </button>
                    </div>
                </div>

                <div v-show="whatssapChecked" class="col-lg-12 mt-2 mb-2 text-center">
                    <img :src="(whatsappDatas!='' && whatsappDatas.picurl!='')? whatsappDatas.picurl : 'images/contacts/default.png'" class="img-fluid whatsappImageProfile" alt="">
                    <br><br>
                    <span class="fa fa-check fa-2x" style="color:green"> </span> Verificado
                </div>
                
                <div class="col-lg-12 mt-4">
                    <div class="form-group">
                        <div  class="form-group has-search">
                            <span class="fa fa-user form-control-feedback"></span>
                            <input v-model="model.first_name" title="Ex: Nome do Contato" id="first_name" name="first_name" type="text" autofocus placeholder="Nome completo" class="form-control outline"/>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                            <span class="mdi mdi-email-outline form-control-feedback"></span>
                            <input v-model="model.email" title="Ex: contato@gmail.com" name="email" id="email" type="text" placeholder="Email" class="form-control outline"/>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                            <span class="fa fa-phone form-control-feedback"></span>
                            <input v-model="model.phone" v-mask="'###############'" title="Ex: 551188888888" id="phone" name="phone" type="text" placeholder="Telefone fixo" class="form-control outline"/>
                        </div>
                    </div>
                </div> 
                
                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <input v-model="model.cidade" title="Ex: Niterói" id="cidade" name="cidade" type="text" placeholder="Cidade" class="form-control"/>
                        </div>
                    </div>
                </div>   
                
                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                        <span class="fa fa-id-card form-control-feedback"></span>
                        <input v-model="model.estado" title="Ex: Rio de Janeiro" id="estado" name="estado" type="text" placeholder="Estado" class="form-control"/>
                        </div>
                    </div>
                </div>   
                
                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <input v-model="model.categoria1" title="Ex: categoria1" id="categoria1" name="categoria1" type="text" placeholder="Categoria 1" class="form-control"/>
                        </div>
                    </div>
                </div>   
                
                <div class="col-lg-12">
                    <div class="form-group">
                        <div style="" class="form-group has-search">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <input v-model="model.categoria2" title="Ex: categoria2" id="categoria2" name="categoria2" type="text" placeholder="Categoria 2" class="form-control"/>
                        </div>
                    </div>
                </div>   

                <div  class="col-lg-12 mt-5">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="javascript:void(0)" @click.prevent="show_sumary=!show_sumary" v-show="show_sumary==false" class="box"><i class="fa fa-angle-double-down"></i> Resumo</a>
                            <a href="javascript:void(0)" @click.prevent="show_sumary=!show_sumary" v-show="show_sumary==true" class="box"><i class="fa fa-angle-double-up"></i> Resumo</a>
                        </li>
                        <li class="list-inline-item float-right">
                            <a href="javascript:void(0)" @click.prevent="show_remember=!show_remember" v-show="show_remember==false" class="box"><i class="fa fa-angle-double-down"></i> Lembrete</a>
                            <a href="javascript:void(0)" @click.prevent="show_remember=!show_remember" v-show="show_remember==true" class="box"><i class="fa fa-angle-double-up"></i> Lembrete</a>
                        </li>
                    </ul>
                </div>
                <div  class="col-lg-12">
                    <div v-show="show_sumary==true" class="form-group">
                        <textarea v-model="model.summary" @keyup="countLengthSumary" name="summary" id="summary" placeholder="Adicione um resumo ..." class="form-control" cols="30" rows="3"></textarea>
                        <label class="form-group has-search-color" for="form-group">{{summary_length}}/500</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div v-show="show_remember==true" class="form-group">
                        <textarea v-model="model.remember" @keyup="countLengthRemember" name="remember" id="remember" placeholder="Adicione um lembrete ..." class="form-control" cols="30" rows="3"></textarea>
                        <label class="form-group has-search-color" for="form-group">{{remember_length}}/500</label>
                    </div>
                </div>

                <div class="col-lg-12 mt-2 text-center">
                    <button v-if='action=="insert"' type="button" class="btn btn-primary btn_width" :disabled='isSendingInsert || !whatssapChecked' @click.prevent="addContact">
                        <i v-show="isSendingInsert==true" class="fa fa-spinner fa-spin" style="color:white" ></i> Adicionar
                    </button>

                    <button v-if='action=="edit"' type="button" class="btn btn-primary btn_width" :disabled='isSendingUpdate' @click.prevent="updateContact">
                        <i v-show="isSendingUpdate==true" class="fa fa-spinner fa-spin" style="color:white" ></i> Atualizar
                    </button>
                    <button type="reset"  class="btn btn-effect-ripple btn-secondary btn_width reset_btn1" @click.prevent="formReset();toggle_left('close')">Cancelar</button>
                </div>
            </form>
        </form>
        <form v-show="action=='delete'">
            Tem certeza que deseja remover esse contato?
            <div class="col-lg-12 mt-5 text-center">
                <button type="button" class="btn btn-primary btn_width" :disabled="isSendingDelete==true" @click.prevent="deleteContact">
                    <i v-show="isSendingDelete==true" class="fa fa-spinner fa-spin" style="color:white" ></i>Eliminar
                </button>
                <button type="reset" class="btn  btn-secondary btn_width" @click.prevent="formCancel">Cancelar</button>
            </div>                    
        </form>
        <form v-show="action=='transfer'">
            <h6> Tranferir contato para: <b>{{(selectedAttendantToTransfer)? selectedAttendantToTransfer.user.name : ""}}</b></h6>
            <v-scroll :height="100"  color="#ccc" class="margin-left:0px" style="background-color:white" bar-width="8px">
                <ul v-for="(attendant,index) in attendants" :key="index" class="list-group list-group-horizontal pointer-hover" @click.prevent="selectedAttendantToTransfer = attendant">
                    <li v-if="attendant.user_id != userLogged.id" class="list-group-item border-0">
                        <img :src="attendant.user.image_path" width="50px" height="50px" class="my-rounded-circle mt-1 " alt="Foto">
                    </li>
                    <li v-if="attendant.user_id != userLogged.id" class="list-group-item border-0">
                        <span style="font-size:1em">{{attendant.user.name}}</span>
                        <br><span class="text-muted" style="font-size:0.8em">{{attendant.user.email}}</span>
                    </li>
                    <li v-if="attendant.user_id != userLogged.id" class="ml-5">
                        <i v-show="selectedAttendantToTransfer && selectedAttendantToTransfer.user_id == attendant.user_id" class="fa fa-check fa-2x mt-3" style="color:green"></i>
                    </li>
                </ul>
            </v-scroll>
            <br><br><br><br>
            <div class="col-lg-12 mt-5 text-center">
                <button type="button" class="btn btn-primary btn_width" @click.prevent="transferContact">
                    <i v-if="isTransferingContact==true" class="fa fa-spinner fa-spin"></i>
                    Transferir
                </button>
                <button type="reset" class="btn  btn-secondary btn_width" @click.prevent="formCancel">Cancelar</button>
            </div>                    
        </form>
    </div>
</template>
<script>
    import Vue from 'vue';
    import VueForm from "vue-form";
    import options from "src/validations/validations.js";
    import ApiService from "resources/common/api.service";    
    import vScroll from "../../../plugins/scroll/vScroll.vue";
    import miniToastr from "mini-toastr";
    miniToastr.init();

    import validation from "src/common/validation.service";

    Vue.use(VueForm, options);
    export default {
        name: "add_user",

        props:{
            action:'',
            item:{},
        },

        components:{
            vScroll
        },

        data() {
            return {
                //---------General properties-----------------------------
                userLogged:{},
                url:'contacts',  //route to controller
                secondUrl:'attendantsContacts',  //route to controller
                
                //---------Specific properties-----------------------------
                contact_id: "",
                contact_atendant_id: "",

                //---------New record properties-----------------------------
                
                model:{
                    first_name: "",
                    last_name: "",
                    phone: "",
                    email: "",
                    description: "",
                    remember: "",
                    summary: "",
                    whatsapp_id: "",
                    json_data: "",
                    facebook_id: "",
                    instagram_id: "",   
                    linkedin_id: "",
                    origin: "",
                    
                    cidade: "",
                    estado: "",
                    categoria1: "",
                    categoria2: "",
                },

                isSendingInsert: false,
                isSendingUpdate: false,
                isSendingDelete: false,
                isCheckingWhatsapp: false,
                whatssapChecked: false,
                whatsappDatas:'',

                flagReference: true,

                attendants:null,
                selectedAttendantToTransfer:null,
                isTransferingContact:false,

                summary_length:0,
                remember_length:0,

                show_sumary:false,
                show_remember:false,

                formstate: {},
                show_error:false,
                show_success:false,
                validationErrors:[],
            }
        },

        methods: {
            addContact: function () {
                if(!this.whatssapChecked){
                    miniToastr.warn("Atenção","Precisa informar e conferir o número de Whatsapp");  
                    return;
                }

                this.model.id=4; //TODO: el id debe ser autoincremental, no devo estar mandandolo
                this.model.status_id = 1;
                this.contact_atendant_id = JSON.parse(localStorage.user).id;
                this.isSendingInsert = true;

                // Validando dados
                this.trimDataModel();
                this.validateData();
                if (this.flagReference == false){
                    // miniToastr.error("Erro", 'Por favor, confira os dados inseridos' );
                    this.isSendingInsert = false;
                    this.flagReference = true;
                    return;
                }
                this.model.origin = 2;
                var model_cpy = Object.assign({}, this.model);
                model_cpy.whatsapp_id = model_cpy.whatsapp_id.replace(/ /g, '');
                model_cpy.whatsapp_id = model_cpy.whatsapp_id.replace(/-/i, '');

                ApiService.post(this.url,model_cpy)
                    .then(response => {
                        if (this.contact_atendant_id) {
                            ApiService.post(this.secondUrl,{
                                'id':1, //TODO: el id debe ser autoincremental, no devo estar mandandolo
                                'contact_id':response.data.id,
                                'attendant_id':this.contact_atendant_id,
                            })
                            .then(response2 => {
                                this.whatssapChecked = false;
                                miniToastr.success("Contato adicionado com sucesso.","Sucesso");
                                this.formReset();
                                this.toggle_left('close');
                                this.$emit('insertContactAsFirtInList', response.data);
                            })
                            .catch(error => {
                                this.processMessageError(error, this.secondUrl, "add");
                            })
                            .finally(() => this.isSendingInsert = false);
                        }
                    })
                    .catch(error=> {
                        this.processMessageError(error, this.url, "add");
                    })
                    .finally(() => this.isSendingInsert = false);
            },

            editContact: function(){
                this.model = Object.assign({}, this.item);
                this.contact_id =  this.item.id;
            },

            deleteContact(){
                this.isSendingDelete = true;
                ApiService.delete(this.url+'/'+this.item.id)
                .then(response => {                        
                    miniToastr.success("Contato eliminado com sucesso","Sucesso");
                    this.$emit('removeContactFromList',this.item.id);
                    this.formCancel();
                })
                .catch(error => {
                    this.processMessageError(error, this.url, "delete");
                })
                .finally(() => this.isSendingDelete = false);  
            },

            transferContact: function(){
                if(!this.selectedAttendantToTransfer){
                    miniToastr.warn("Atenção", "Você deve selecionar um atendente."); 
                    return;
                }
                if(!this.isTransferingContact){
                    this.isTransferingContact = true;
                    ApiService.post(this.secondUrl,{
                        'id':0,
                        'attendant_id':this.selectedAttendantToTransfer.user.id,
                        'contact_id':this.item.id,
                        'transfering':true
                    })
                    .then(response => {
                        miniToastr.success("Contato tranferido com sucesso","Sucesso");
                        this.$emit('removeContactFromList',this.item.id);
                        this.formCancel();
                    })
                    .catch(error => {
                        this.processMessageError(error, this.secondUrl, "transferring");
                    })
                    .finally(() => this.isTransferingContact = false);   
                }
            },

            getAttendants: function(){
                ApiService.get('usersAttendants')
                    .then(response => {                        
                        this.attendants = response.data;
                    })
                    .catch(error => {
                        this.processMessageError(error, "usersAttendants", "get");
                    }); 
            },

            checkWhatsappNumber:function(){
                this.isCheckingWhatsapp = true;

                if(this.model.whatsapp_id !=''){
                    var check = validation.check('whatsapp', this.model.whatsapp_id)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.isCheckingWhatsapp = false;
                        return;
                    }
                }else{
                    miniToastr.error("Erro", "O número de Whatsapp é obrigatório" );
                    this.isCheckingWhatsapp = false;
                    return;
                }

                var model_cpy = Object.assign({}, this.model);                      //ECR: Para eliminar espaços e traços
                model_cpy.whatsapp_id = model_cpy.whatsapp_id.replace(/ /g, '');    //ECR
                model_cpy.whatsapp_id = model_cpy.whatsapp_id.replace(/-/i, '');    //ECR

                ApiService.get('RPI/getContactInfo/'+model_cpy.whatsapp_id)
                    .then(response => {
                        this.whatsappDatas = response.data;
                        if(response.data.picurl.length==0)
                            response.data.picurl = "images/contacts/default.png";
                        this.model.first_name = response.data.name;
                        this.model.json_data = JSON.stringify(response.data);
                        this.whatssapChecked = true;
                        this.isCheckingWhatsapp = false;
                        miniToastr.success("Número de Whatsapp conferido com sucesso","Sucesso");
                    })
                    .catch(error => {
                        this.processMessageError(error, "getContactInfo", "get");
                    })
                    .finally(() => {this.isCheckingWhatsapp = false;});
            },

            formReset:function(){
                this.model.first_name = "";
                this.model.last_name = "";
                this.model.email = "";
                this.model.description = "";
                this.model.remember = "";
                this.model.summary = "";
                this.model.phone = "";
                this.model.whatsapp_id = "";
                this.model.facebook_id = "";
                this.model.instagram_id = "";
                this.model.linkedin_id = "";
                this.model.json_data = "";
            },

            countLengthSumary: function(){
                this.model.summary = this.model.summary.length > 500 ? this.model.summary.substring(0, 500) : this.model.summary;
                this.summary_length = this.model.summary.length;                    
            },

            countLengthRemember: function(){
                this.model.remember = this.model.remember.length > 500 ? this.model.remember.substring(0, 500) : this.model.remember;
                this.remember_length = this.model.remember.length;
            },

            toggle_left(action) {
                this.$store.commit('leftside_bar', action);
            },

            toggle_right() {
                this.$store.commit('rightside_bar', "toggle");
            },

            formCancel(){
                this.$emit('onclosemodal');
            },
            
            trimDataModel: function(){
                if(this.model.first_name) this.model.first_name = this.model.first_name.trim();
                if(this.model.last_name) this.model.last_name = this.model.last_name.trim();
                if(this.model.email) this.model.email = this.model.email.trim();
                if(this.model.phone) this.model.phone = this.model.phone.trim();
                if(this.model.whatsapp_id) this.model.whatsapp_id = this.model.whatsapp_id.trim();
                if(this.model.facebook_id) this.model.facebook_id = this.model.facebook_id.trim();
                if(this.model.instagram_id) this.model.instagram_id = this.model.instagram_id.trim();
                if(this.model.linkedin_id) this.model.linkedin_id = this.model.linkedin_id.trim();
                if(this.model.remember) this.model.remember = this.model.remember.trim();
                if(this.model.summary) this.model.summary = this.model.summary.trim();
                if(this.model.description) this.model.description = this.model.description.trim();
            },

            validateData: function(){
                // Validação dos dados do contato
                var check;
                if(this.model.first_name && this.model.first_name !=''){
                    check = validation.check('complete_name', this.model.first_name)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                
                if(this.model.last_name && this.model.last_name !=''){
                    check = validation.check('complete_name', this.model.last_name)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                if(this.model.email && this.model.email !=''){
                    check = validation.check('email', this.model.email)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                if(this.model.phone && this.model.phone !=''){
                    check = validation.check('phone', this.model.phone)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                if(this.model.whatsapp_id && this.model.whatsapp_id !=''){
                    check = validation.check('whatsapp', this.model.whatsapp_id)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }else{
                    miniToastr.error("Erro", "O whatsapp do contato é obrigatório" );
                    this.flagReference = false;
                }
                if(this.model.facebook_id && this.model.facebook_id !=''){
                    check = validation.check('facebook_profile', this.model.facebook_id)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                if(this.model.instagram_id && this.model.instagram_id !=''){
                    check = validation.check('instagram_profile', this.model.instagram_id)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
                }
                if(this.model.linkedin_id && this.model.linkedin_id !=''){
                    check = validation.check('linkedin_profile', this.model.linkedin_id)
                    if(check.success==false){
                        miniToastr.error("Erro", check.error );
                        this.flagReference = false;
                    }
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
            }
        },

        beforeMount(){
            if(this.action=='edit'){
                this.editContact();
            }
            if(this.action=='transfer'){
                this.editContact();
                this.getAttendants();
            }
            this.userLogged = JSON.parse(window.localStorage.getItem('user'));
        },

        mounted(){
            if(this.userLogged.role_id > 4){
                this.$router.push({name: "login"});
            }
        },

        created() {
            miniToastr.setIcon("error", "i", {class: "fa fa-times"});
            miniToastr.setIcon("warn", "i", {class: "fa fa-exclamation-triangle"});
            miniToastr.setIcon("info", "i", {class: "fa fa-info-circle"});
            miniToastr.setIcon("success", "i", {class: "fa fa-arrow-circle-o-down"});
        },

        destroyed: function () {

        }
    }
</script>

<style scoped>
    .dropzone_wrapper {
        width: 100%;
    }

    .align-left {
        float: left;
    }

    .align-right {
        float: right;
    }
    /*-------------------------------------*/
    .sect_header{
        /* background-color:#eaf5ff; */
        background-color:white;
        /* height:70px;   */
        height:6.5rem;  
        /* border: 1px solid #e9e9e9; */
        border-bottom: 1px solid #e9e9e9;
        padding-top:10px;
    }
    /*-------------------------------------*/
    .menu{
        z-index: 100;
        list-style:none; 
        margin: 0;
        padding: 0;        
    }
    .menu li{
        position:relative; 
        float:left; 
        
    }
    .menu li a,p{
        font-size: 12px;
        display: block;
        color: gray;
        text-align: left;
        padding: 8px;
        padding-top: 16px;
        text-decoration: none;
    }    
    .menu, li, a, a:active, a:focus {
        outline: none;
    }
    /*-------------------------------------*/
    .has-icon .form-control {
        padding-left: 2.375rem;
    }
    .has-icon .form-control {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
        color: #aaa;
    }  

    .has-search .form-control {
        padding-left: 2.375rem;
    }
    .has-search .form-control-feedback {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
        color: #aaa;
    }  

    .has-search .form-control-feedback-2 {
        position: absolute;
        right: 0px;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
        color: #aaa;
    }  
    /*-------------------------------------*/
    .btn_width{
        width: 100px
    }

    .has-search-color{
        color: #aaa;
    }

    .box{
        font-size: 12px;
        color: gray;
    }

    .header-title{
        font-size: 1.3em;
    }

    .my-rounded-circle{
        border-radius: 50%;
        width: 50px;
        width: 50px;
        padding: 0px !important;
        margin: 0px !important;
    }
    .whatsappImageProfile{
        border-radius: 50%;
        width: 7em;
        width: 7em;
        padding: 0px !important;
        margin: 0px !important;
    }

    .mouse-over:hover{
        cursor: pointer;
        background-color: #fafafa !important;
    }

    .outline:focus{
        outline: 0 !important;
        box-shadow: none !important;
        border: 1px solid silver;
    }
    .pointer-hover:hover{
        cursor: pointer;
    } 

</style>
