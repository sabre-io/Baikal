var Collection = require('backbone').Collection,
    Addressbook = require('./Addressbook');

var AddressbookCollection = Collection.extend({
    model: Addressbook,
    
    apiurl: null,
    url: function() { return this.apiurl + '/addressbooks';},

    configure: function(options) { this.apiurl = options.apiurl;},

    parse: function(response) {
        return response.addressbook;
    }
});

module.exports = AddressbookCollection;