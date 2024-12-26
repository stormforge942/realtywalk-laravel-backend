<template>
  <div>
    <!-- name="fade" mode="out-in" -->
    <transition>
      <router-view></router-view>
    </transition>
    <vue-progress-bar></vue-progress-bar>
  </div>
</template>

<style>

.builders-list {
    margin-left:10px;
}


.limit-height {
    scrollbar-color: gray none;
    scrollbar-width: thin;

}

.limit-height::-webkit-scrollbar
{
    width: 8px;

}
.limit-height::-webkit-scrollbar-thumb
{
    border-radius: 10px;
    background-color: rgb(211, 211, 211);
}

body, html, .c-app, .builders{
    height: 100% !important;
}
/** get the main dialog window */
.modal-dialog {
    max-width: 70%;
}
/** get the dialog inside the button */
.schedule-listing .modal-dialog {
   max-width: 40%;
}

.property-list{
    display: flex;
    width: 100%;
    margin-bottom: 15px;
    overflow: hidden;
    height: 242px;
}

.property-list > div:first-of-type {
    width: 40%;
}

.property-list > div > img {
    width: 100%;
   height: 242px;
   object-fit: cover;
}

button:active,
button:focus {
    box-shadow: none;
    outline: 0;
}


.property-details {

    width: 100%;
    height: 242px !important;
    background: #d7e4ea;
}

.property-details > div  {
    padding-left: 40px;
    display: flex;
    justify-content: space-between;
}

.property-details h6, .property-details h3 {
    color: #012e55;
}
 .property-details h3 {
     font-size: 40px;
 }

 .property-details h6 {
     font-size: 20px;
 }
  h6 > a {
    color: #012e55;
    text-decoration: underline;
  }


.property-details > div > div {
    padding-left: 25px;
    display: flex;
    justify-content: space-between;
}

.right-buttons{
    border-left: 2px solid #fff;
    height: 100%;
}

.right-buttons button {
    border:none;
    background: none;
    width: 120px;
    height: 120px;
}

.calendar-button {
       border-top: 2px solid #fff !important;
}


.right-buttons button img {
    width: 25px;
}
/*
.property-details {
    border:1px solid red;
    width: 100%;
    background: #d7e4ea;

}
.property-details h6, .property-details h3 {
    color: #012e55;
}
 .property-details h3 {
     font-size: 40px;
 }

 .property-details h6 {
     font-size: 20px;
 }

.property-details > div > div {
    padding-left: 40px;
    display: flex;
    justify-content: space-between;
}

.property-desc {
    margin-top: 60px;
}
.right-buttons{
    width: 40%;
    border-left: 2px solid #fff;
    height: 100%;
    border: 1px solid green;
}

.right-buttons button {
    border:none;
    background: none;
    width: 120px;
    height: 120px;
}

.calendar-button {
       border-top: 2px solid #fff !important;
}


.right-buttons button img {
    width: 25px;
}


*/

.fade-enter-active,
.fade-leave-active {
    transition-duration: 0.3s;
    transition-property: opacity;
    transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
    opacity: 0;
}

.row {
    margin: 0px !important;
    width: 100%;
}

.featured-img{
    width: 100%;
    min-height: 450px;
    z-index: 1;
}

.featured-img > div {
    width: 100%;
}

.featured-img-inner {
    display: flex;
}

.featured-img-inner-cover{
    width: 100%;
}

.featured-img-inner-cover h3 {
    padding-bottom: 10px;
    padding-left: 15px;
}

.featured-img-inner-cover img {
    width: 100%;
}

.featured-img-inner-cover-image {
    background-repeat: no-repeat;
    background-size: cover, contain;
    background-position-y: center;
}

.inline-logo {
    display: inline-block;
    width: 150px;
}

.card-text.video-embed {
    margin-bottom: 30px;
}

.item {
    width: 27.5%;
}

.modal-body {
    padding: 1rem;
}


.modal-body .form-control {
    padding: .375rem .75rem;
}

.padding-left {
    padding-right: 80px;
}

.list-group-item {
    display: inline-block;
    border: 0;
    background-color: transparent;
    padding: 0.55rem 0rem;
    color: rgba(0, 0, 0, 0.7);

}
.more-images {
    display: block;
    width: 105px;
    color: #fff;
    font-size: 0.85em;
    padding-left: 10px;
    margin-top: 10px;
}
.more-images:hover {
    text-decoration: none;
    color: #7d8e98;
}


.header-map {
    position: relative;
    height: 400px;
}
/*
.list-call {
    display: flex;
    align-items: center;
    padding: 1.25rem 0px;

}

.list-call.call {
    position: relative;
    z-index: 2;
    margin-top: -50px;
}
*/

/*
.list-call > a {
    padding-left: 5%;
}
*/



@media screen and (max-width: 991px) {


    .card-text.video-embed {
        padding-bottom: 0px !important;
        margin-bottom: 0px !important;
    }

    .featured-img-inner-cover {
        width: 100%;
    }

    .featured-img-inner-cover h3 {
        background: #033561;
        padding: 10px 0px 10px 15px;
        margin-bottom: -2px;
    }
    .padding-left {
        padding-right: 0px;
    }


    #section-two {
        padding: 0px;
    }

    .feature-div-container > div {
        width: 100%;
        padding-left: 0%;
        padding: 10px;
    }

    .map, #section-two > div, .feature-div-container, .statistics-div-container{
        margin: 0px;
        padding:0px;

    }

    .list-call {
        padding: 0px !important;
    }


    .features-box, .feature-div-container, .neighborhood-desc, .call {
        width: 100%;
        padding: 0px;
    }

    .card-info {
        display: block;

    }

    .card-text {
        padding-right: 6% !important;
    }

    .inner-features {
        justify-content: start;

    }

    .inner-features > div:first-of-type {
        margin-right: 30%;
    }

    .features-box .card .card-body, .card .card-body .card-text, .mobile-gallery {
       /* padding: 1.25rem 1rem;*/
       padding-top: 3.55rem;
       padding-bottom: 3.55rem;
       padding-left: 5%;
    }
    .mobile-gallery-inner {
        width: 90%;

    }

    .mobile-title {
        margin: 1rem 0;
        font-size: 2.5rem;
        letter-spacing: 2px;
    }
    .mobile-paddingless {
        padding-left: 0;
        padding-right: 0;
        padding-top: 2rem;
        padding-bottom: 2rem;
        background: #f0f4f7;
    }

    .list-group-item {
        font-size: 1.5rem;
    }

    .mobile-contents {
        padding: 2rem 0;
    }

    .mobile-contents .card-text {
        font-size: 1.2rem;
        color: rgba(0, 0, 0, 0.7);
        /*padding: 3rem 0;*/
    }
    .custom-mx-sm-0 {
        margin-right: 0px;
        margin-left: 0px;
    }

    #content {
        padding-top: 120px !important;

    }
}

@media screen and (min-width: 992px) and (max-width: 1510px) {
    .no-margin {
        margin-left: 0px;
        margin-right: 0px;
    }
}

@media screen and (max-width: 1034px) {
    .grey-btn a {
        font-size: 0.75em;
    }
    .map {
       padding:0px;
    }
}

@media screen and (max-width: 1458px) {
    .back-map a {
        padding: 0.25rem 1rem;
    }
}

@media screen and (max-width: 768px) {

    .feature-div-container > div {
        width: 100%;
        padding: 0px;
    }
    .custom-mx-sm-0 {
        margin-right: 0px;
        margin-left: 0px;
    }
    .featured-img h3 {
        font-size: 1.2em;
    }
    .thumbnail-img img {
        height: 300px;
    }



    .property-list {
        display: block;
        width: 100%;
        overflow: visible;
        height: auto;
        margin-bottom: 0px;
    }

   .property-list > div > img {
        width: 100%;
        height: auto;
    }
    .property-list > div:first-of-type {
        width: 100%;
    }
    .modal-dialog {
        max-width: 100% !important;
        width: 100%;
    }
}

@media screen and (max-width: 380px) {
    .property-details > div {
      padding-left: 0;
    }

    .property-details > div > div {
      padding-left: 15px;
    }

    .modal-dialog {
        max-width: 380px !important;
        width: auto;
        margin:0px !important;
    }

    .item {
        width: 40%;
    }

    .features-box {
        min-width: auto;
    }

    .featured-img-inner-cover {
        overflow: hidden;

    }

    .featured-img-inner-cover img {
     transform: scale(2.1);
     margin-top: 112px;
     height: 200px;
    }

    .inner-features {
        display: block;
    }
    .inner-features > div:first-of-type {
        margin-right: 0px;
    }
}

</style>
