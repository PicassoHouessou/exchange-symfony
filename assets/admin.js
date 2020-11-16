import "./styles/admin.css";

var $ = require("./../public/admin/plugins/jquery/jquery.min.js");


var jqueryUi = require("./../public/admin/plugins/jquery-ui/jquery-ui.min.js");

require("./../public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js");

require("./../public/admin/plugins/summernote/summernote-bs4.min.js");

require("./../public/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js");

var DataTable =require("./../public/admin/plugins/datatables/jquery.dataTables.min.js");
require("./../public/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");

require("./../public/admin/dist/js/adminlte.min.js");
// require("./../public/vendors/vue/vue.js") ;
// require("./../public/vendors/axios/axios.js") ;
// require("./../public/vendors/vue-loading-overlay/vue-loading-overlay@3.js") ;

import Vue from "vue" ;
import axios from "axios";
import loading from "vue-loading-overlay" ;

Vue.component("loading", loading);

global.$ = global.jQuery = $;
global.$.widget = global.jQuery.widget = jqueryUi ;
global.$.ui = global.jQuery.ui = jqueryUi.ui;

$.widget.bridge('uibutton', $.ui.button);

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

global.Vue = Vue;
global.axios = axios;
global.loading = loading;

console.log('Hello Webpack Encore! Edit me in assets/app.js');

