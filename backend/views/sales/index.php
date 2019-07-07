<?php
backend\assets\SalesAsset::register($this);
$this->registerJs('Sales.init()');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = $title;
$this->params['breadcrumbs'] = [
    [
        'label' => $title,
        'url' => ['/sales']
    ],
    $this->title
];
?>
<div id="sale" v-cloak >
    <div  class="row">
        <div class="box" style="margin-bottom: 0px;">
            <div v-show="Cargando" class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-body">
                <div id="errores" v-cloak> 
                        <div v-if="Error"> 
                            <div v-bind:class="'alert alert-' + Error.Type">
                                <button type="button" class="close" @click=" Error = null;">×</button>
                                <div v-html="Error.Text"></div>
                            </div>
                        </div>
                    </div>
                <div id="discounts" v-cloak> 
                        
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>Product: </label>
                        <select v-if="Products" v-model="Item.IdProduct" class="form-control">
                            <option v-bind:value= "null" selected hidden >Choose a Product</option>
                            <option   v-for="Product in Products" v-bind:value="Product.IdProduct">{{Product.Product}}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Number: </label>
                        <input v-model="Item.Number" v-on:keyup.enter="AddProduct()" type="number">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary text-center" @click="AddProduct()">+Add</button>
                    </div>
                </div>
                <div class="row">
                    <hr style="border-top: 1px solid #3c8dbc;border-width:3px">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-responsive table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody  v-for="(product) in Sale.Products" >
                                <tr v-if="product">
                                    <td>{{ product.Product}}</td>
                                    <td>{{ product.Description}}</td>
                                    <td> &euro;{{ product.Price }}</td>
                                    <td>{{ product.Number }}</td>
                                    <td><button  class="btn btn-default" @click='RemoveProduct(product.IdProduct);' data-hint='Remove'><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tr v-show="Sale.FinalPrice">
                                <td class="padding-lateral">Final Price </td>
                                <td class="padding-lateral">&euro;{{Sale.FinalPrice}}</td>
                            </tr>  
                            <tr v-show="Sale.Discounts > 0" style="color: red">
                                <td class="padding-lateral">Discounts</td>
                                <td class="padding-lateral">&euro;{{Sale.Discounts}}</td>
                            </tr> 
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>