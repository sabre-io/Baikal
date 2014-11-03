var Collection = require('backbone').Collection,
    Contact = require('./Contact');

var ContactCollection = Collection.extend({
    model: Contact,
    
    addressbook: null,
    url: function() { return this.addressbook.url() + '/contacts' ;},

    comparator: function(model) {
        return model.getDisplayName().toUpperCase();
    },

    configure: function(options) {
        this.addressbook = options.addressbook;
    },

    parse: function(response) {
        return response.contact;
    }
});

module.exports = ContactCollection;