var Model = require('backbone').Model;

var getCaps = function(string, number) {

    var caps = [];
    if(!number) number = 2;
    var words = string.split(/\s+/);
    if(words.length == 1) {
        return [words[0].substring(0, 1).toUpperCase(), words[0].substring(1, 2).toUpperCase()];
    }

    for(var index in words) {
        caps.push(words[index].substring(0, 1).toUpperCase());
    }

    return (caps.length < number) ? caps : caps.slice(0, number);
};

var Contact = Model.extend({
    getDisplayName: function() {

        if(this.company()) {
            var companyname = this.getCompanyName();
            if(companyname) return companyname;
        }

        return this.getContactName();
    },
    getContactName: function() {
        return (
            this.get('n').honorificprefix + ' ' +
            this.get('n').firstname + ' ' +
            this.get('n').additionalname + ' ' +
            this.get('n').lastname + ' ' + 
            this.get('n').honorificsuffix
        ).trim();
    },
    company: function() {
        return this.get('company') === true;
    },
    getCompanyName: function() {
        var org = this.get('org');
        if(org && org.name && org.name.trim() !== '') return org.name;
        return null;
    },
    getDepartment: function() {
        var org = this.get('org');
        if(org && org.units.length) return org.units.join(', ');
        return null;
    },
    getCaps: function() {
        //var caps = (this.get('n').firstname.substring(0, 1) + this.get('n').lastname.substring(0, 1)).trim().toUpperCase();
        return getCaps(this.getDisplayName()).join('');
    }
});

module.exports = Contact;