'use strict';

var _ = require('underscore');

module.exports = function(types, searchedtypes) {
    return _.find(types, function(item) {
        item = item.toLowerCase();
        for(var searchedtypeindex in searchedtypes) {
            if(item == searchedtypes[searchedtypeindex].toLowerCase()) { return true;};
        }

        return false;
    });
};