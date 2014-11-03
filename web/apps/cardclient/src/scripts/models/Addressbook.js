var Model = require('backbone').Model;
var Addressbook = Model.extend({
    defaults: {
        id: null,
        displayname: '',
        description: ''
    }
});

module.exports = Addressbook;