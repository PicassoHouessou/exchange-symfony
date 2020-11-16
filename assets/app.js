import "./styles/app.css";

//var $ = require('./../public/assets/vendor/jquery/jquery-3.2.1.min.js') ;
var $ = require("jquery");
global.$ = global.jQuery = $ ;

require("./../public/assets/vendor/animsition/js/animsition.min.js") ;
require("./../public/assets/vendor/bootstrap/js/popper.js") ;
require("./../public/assets/vendor/bootstrap/js/bootstrap.min.js") ;
require("./../public/assets/js/main.js") ;

//var Vue = require('./../public/vendors/vue/vue.js') ;
/*
var axios = require('./../public/vendors/axios/axios.js') ;
var VueLoading = require('./../public/vendors/vue-loading-overlay/vue-loading-overlay@3.js') ;
var VeeValidate = require('./../public/vendors/veevalidate/vee-validate-3.3.11-full.min.js') ;

 */
import Vue from "vue" ;
import axios from "axios";
import loading from "vue-loading-overlay" ;
//import { ValidationProvider } from "vee-validate" ;
import { ValidationProvider } from "vee-validate/dist/vee-validate.full.esm" ; // pour avoir toutes les regles
import { extend } from "vee-validate/dist/vee-validate.full.esm"; // Pour mes propres regles

extend('checked', (value) => {
    if (value === true) {
        return true ;
    } else {
        return ' The {_field_} field must be checked';
    }

});
extend('appPassword', (value) =>  {
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)/ ;
    if (regex.test(value)){
        return true ;
    } else {
        return ' The {_field_} field must content a tiny, a capital letter, a special character and a number' ;
    }

});
import { ValidationObserver } from "vee-validate" ;


/*

var axios = require('axios');
var VueLoading = require ('vue-loading-overlay');
var VeeValidate = require ('vee-validate');
*/

/*
import { ValidationProvider } from 'vee-validate';
Vue.component('validation-provider', ValidationProvider);

 */
Vue.component("validation-provider", ValidationProvider);

Vue.component("validation-observer", ValidationObserver);
Vue.component("loading", loading);

//Vue.config.performance = true ;
global.Vue = Vue ;
global.axios = axios ;
//global.VueLoading = VueLoading ;
global.loading = loading ;

//global.VeeValidate = VeeValidate ;

console.log('Hello Webpack Encore! Edit me in assets/app.js');

