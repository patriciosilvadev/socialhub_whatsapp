<template>
    <div class="login">
        <article class="login_box radius">
            <div style="display: flex; align-items: center; justify-content:center" class="text-center">
                <a href="javascript:void()" @click.prevent="goToHome">
                    <!-- <img src="~img/socialhub/sh.jpeg" style="height:4rem; width:4.4rem; border-radius:50%" alt=""> -->
                </a>
                <h1 class="hl ml-2">Senha</h1>
            </div>
            <vue-form :state="formstate" @submit.prevent="onSubmit">
                <input type="hidden" id="token" v-model="model.token" required/>
                <label>
                    <span class="field icon-unlock-alt">Nova senha:</span>
                    <input name="password" type="password" placeholder="Nova senha:" id="password" autofocus  v-model="model.password" required/>
                </label>
                
                <label>
                    <span class="field icon-unlock-alt">Repetir senha:</span>
                    <input name="password" type="password" placeholder="Repetir senha:" id="password"  v-model="model.password_confirmation" required/>
                </label>

                <button type="submit" id="btnEnter" class="radius bg-socialhub icon-sign-in" :disabled="isSendingResetPassword==true" @click.prevent="onSubmit">
                    <i v-show="isSendingResetPassword==true" class="fa fa-spinner fa-spin" style="color:white" ></i> Enviar
                </button>

                <div class="col-12 mt-3">
                    <div class="login-text text-right">
                        <p>(*) A senha é sensível a maiúsculas.</p>
                    </div>
                </div>
            </vue-form>

            <footer>
                <p>Todos os direitos reservados</p>
                <p class="icon-whatsapp transition"><b>SocialHub</b></p>
            </footer>
        </article>
    </div>    
</template>


<script>
    import Vue from 'vue'
    import VueForm from "vue-form";
    import options from "src/validations/validations.js";
    import ApiService from "resources/common/api.service";
    import miniToastr from "mini-toastr";
    miniToastr.init();

    Vue.use(VueForm, options);
    export default {
        name: "resetpassword",
        data() {
            return {
                errorShow: false,
                successShow: false,
                formstate: {},
                message: '',
                model: {
                    password: "",
                    password_confirmation: "",
                    token: ""
                },

                isSendingResetPassword:false,

            }
        },
        methods: {
            onSubmit: function () {
                var p1=this.model.password.trim();
                var p2=this.model.password_confirmation.trim();
                if (p1.length<6 || p2.length<6) {
                    miniToastr.error('As senhas devem ter pelo menos 6 caracteres', 'Erro'); 
                    return;
                } 
                if (p1 != p2) {
                    miniToastr.error('As senhas não coincidem', 'Erro'); 
                    return;
                } 
                
                let component = this;
                component.message = '';
                this.isSendingResetPassword = true;
                ApiService.post('/auth/password_save', this.model)
                .then(response => {
                    if (!!response.data.success) {
                        component.model = {};
                        miniToastr.success("Senha redefinida com sucesso","Sucesso");
                        setTimeout(()=>{
                            this.$router.push({name: 'login'});
                        },1000);
                    } else {
                        component.model.password = '';
                        component.model.password_confirmation = '';
                        miniToastr.error('Alguns dados incorretos', 'Erro'); 
                    }
                })
                .catch(error => {
                    miniToastr.error(error, 'Exception'); 
                    this.processMessageError(error, "auth/password_save", "add");
                })
                .finally(() => {this.isSendingResetPassword = false;});
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
            goToHome(){
                this.$router.push({name: 'login'});
            }
        },
        beforeMount(){
            this.model.token = this.$route.params.token;
        },
        created() {
            miniToastr.setIcon("error", "i", {class: "fa fa-times"});
            miniToastr.setIcon("warn", "i", {class: "fa fa-exclamation-triangle"});
            miniToastr.setIcon("info", "i", {class: "fa fa-info-circle"});
            miniToastr.setIcon("success", "i", {class: "fa fa-arrow-circle-o-down"});
        },
    }
</script>


<style>
        /*RESET*/
        * {
            margin: 0;
            padding: 0;

            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        /*VARIABLES*/
        :root {
            --font-min: 0.8em;
            --font-small: 0.875em;
            --font-normal: 1em;
            --font-medium: 1.2em;
            --font-large: 1.4em;
            --font-max: 2em;

            --color-default: #555555;
            --color-green: #36BA9B;
            --color-blue: #2EB5EA;
            --color-yellow: #F5B946;
            --color-red: #D94352;
            --color-fsphp: #1D2025;

            --weight-light: 300;
            --weight-normal: 400;
            --weight-strong: 600;
            --weight-bold: 700;
            --weight-black: 900;

            --hover-color-green: #61DDBC;
            --hover-color-blue: #66D4F1;
            --hover-color-yellow: #FCD277;
            --hover-color-red: #F76C82;
            --hover-duration: 0.3s;

            --gradient-green: linear-gradient(to right, #42E695 0%, #3BB2B8 50%, #42E695 100%);
            --gradient-blue: linear-gradient(to right, #2EB5EA 0%, #26C0D4 70%, #8BD6F4 100%);
            --gradient-red: linear-gradient(to right, #622744 0%, #C53364 50%, #622744 100%);
            --gradient-yellow: linear-gradient(to right, #FCE38A 0%, #F38181 50%, #FCE38A 100%);

            --radius-normal: 5px;
            --radius-medium: 10px;
            --radius-large: 20px;
            --radius-round: 50%;

            --index-back: -1;
            --index-fold: 2;
            --index-menu: 3;
            --index-modal: 4;
        }
        /*ELEMENTS*/
        img {
            max-width: 100%;
        }

        img a {
            border: none;
        }

        ul {
            list-style: none;
        }

        p {
            margin: 20px 0 0 0;
        }

        embed,
        video,
        iframe,
        iframe[style] {
            max-width: 100%;
            height: auto;
        }

        .embed {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            max-width: 100%;
        }

        .embed iframe, .embed object, .embed embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .strike {
            text-decoration: line-through;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset;
            -webkit-text-fill-color: #555555 !important;
        }

        .font-zero{ font-size: 0 !important; margin: 0 !important; padding: 0 !important;}

        /*ICONS NORMALIZE*/
        [class^="icon-"]:before,
        [class*=" icon-"]:before {
            position: relative !important;
            top: .125em !Important;
            margin-right: .4em !Important;
        }

        .icon-notext:before {
            top: 0;
            margin-right: 0 !important;;
        }

        @font-face {
            font-family: "fsphp";
            src:url("../../fonts-junior/fsphp.eot");
            src:url("../../fonts-junior/fsphp.eot?#iefix") format("embedded-opentype"),
            url("../../fonts-junior/fsphp.woff") format("woff"),
            url("../../fonts-junior/fsphp.ttf") format("truetype"),
            url("../../fonts-junior/fsphp.svg#fsphp") format("svg");
            font-weight: normal;
            font-style: normal;

        }

        [data-icon]:before {
            font-family: "fsphp" !important;
            content: attr(data-icon);
            font-style: normal !important;
            font-weight: normal !important;
            font-variant: normal !important;
            text-transform: none !important;
            speak: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        [class^="icon-"]:before,
        [class*=" icon-"]:before {
            font-family: "fsphp" !important;
            font-style: normal !important;
            font-weight: normal !important;
            font-variant: normal !important;
            text-transform: none !important;
            speak: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .icon-coffee:before {
            content: "\e000";
        }
        .icon-envelope:before {
            content: "\e006";
        }
        .icon-unlock-alt:before {
            content: "\e01b";
        }
        .icon-sign-in:before {
            content: "\e00b";
        }
        .gradient {
            background-size: 200% auto;
            transition-duration: 0.5s;
        }
        .gradient-green {
            background-image: var(--gradient-green);
        }
        .gradient-hover:hover {
            background-position: right center;
        }
        /*RESET NORMALIZE*/
        * {
            font-family: 'Open Sans', sans-serif;
        }
        p {
            margin: 0;
        }

        /*LOGIN*/
        .login {
            background: #1D2025;
            color: var(--color-default);
            display: flex;
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            padding: 30px 0;
            overflow-x: scroll;
        }
        .login_box {
            width: 400px;
            max-width: 90%;
            margin: auto;
            padding: 35px;
            background: #ffffff;
        }
        .login_box form {
            display: block;
            margin: 0 0 20px 0;
        }
        .login_box .hl {
            text-align: center;
            font-size: 2rem !important;
            font-weight: bold;
            /* font-size: var(--font-max); */
        }
        .login_box label {
            display: block;
            margin-bottom: 20px;
        }
        .login_box label .field {
            display: block;
            font-size: var(--font-small);
            font-weight: bold;
        }
        .login_box label input {
            border: none;
            border-bottom: 1px solid #1D2025;
            outline: none;
            display: block;
            width: 100%;
            padding: 10px 0;
            font-size: var(--font-normal);
        }
        .login_box button {
            display: block;
            cursor: pointer;
            width: 100%;
            padding: 10px;
            border: none;
            color: #ffffff;
            font-size: var(--font-normal);
            font-weight: bold;
            text-transform: uppercase;
            text-shadow: 1px 1px rgba(0, 0, 0, 0.5);
        }
        .login_box footer {
            text-align: center;
            font-size: var(--font-min);
            font-weight: 300;
        }
        .login_box footer a {
            display: block;
            font-size: var(--font-normal);
            color: var(--color-green);
            text-decoration: none;
            font-weight: bold;
            margin-top: 5px;
        }
        .login_box footer a:hover {
            color: var(--hover-color-green);
        }

        /*MESSAGES*/
        .message {
            color: #ffffff;
            font-size: var(--font-normal);
            font-weight: var(--weight-strong);
            text-align: center;

            display: block;
            width: 100%;
            padding: 10px;
            border: 2px solid #cccccc;

            -webkit-border-radius: var(--radius-normal);
            -moz-border-radius: var(--radius-normal);
            border-radius: var(--radius-normal);
        }
        .ajax_response {
            margin: 25px 0;
        }
        .message.success {
            --color: var(--color-green);
            color: var(--color);
            border-color: var(--color);
        }
        .message.info {
            --color: var(--color-blue);
            color: var(--color);
            border-color: var(--color);
        }
        .message.warning {
            --color: var(--color-yellow);
            color: var(--color);
            border-color: var(--color);
        }
        .message.error {
            --color: var(--color-red);
            color: var(--color);
            border-color: var(--color);
        }
        .radius {
            -webkit-border-radius: var(--radius-normal);
            -moz-border-radius: var(--radius-normal);
            border-radius: var(--radius-normal)
        }
        .bg-socialhub{
            background: #3f67af;
        }
    </style>