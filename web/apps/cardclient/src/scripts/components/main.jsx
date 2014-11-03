/** @jsx React.DOM */

'use strict';

var React = require('react');
var {DefaultRoute, Route, Routes} = require('react-router');

// React Components
var App = require('./App'),
    AddressbookInterface = require('./AddressbookInterface');

// Model
var AddressbookModel = require('../models/Addressbook'),
    AddressbookCollection = require('../models/AddressbookCollection'),
    ContactModel = require('../models/Contact'),
    ContactCollection = require('../models/ContactCollection');

// Initialization
var config = JSON.parse(unescape(document.getElementsByName('config/environment')[0]['content']));

var addressbookCollection = new AddressbookCollection();
addressbookCollection.configure({
    apiurl: config.urlapi
});

// Run
addressbookCollection.fetch().then(function() {

    var addressbook = addressbookCollection.get(config.addressbookid);
    var contactCollection = new ContactCollection();

    contactCollection.configure({
        addressbook: addressbook
    });

    React.renderComponent((
        <Routes location="hash">
            <Route name="app" handler={App} addressbook={addressbook}>
                <Route name="addressbookinterface" path="/" handler={AddressbookInterface} contacts={contactCollection} />
            </Route>
        </Routes>
    ), $(config.rootElement || '#content').get(0));
    
    contactCollection.fetch().then(function() {
        contactCollection.sort();
    }).fail(function(error) {
        console.log('Erreur contacts !', error);
    });


}).fail(function(error) {
    console.log('Erreur !', error);
})