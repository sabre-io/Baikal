var axios = require('axios'),
    Promise = require('es6-promise').Promise,
    NProgress = require('nprogress');

var options = {};

var unserializeResponse = function(data) {
    return (data && 'data' in data && 'calendar' in data['data']) ? data['data']['calendar'] : [];
};

module.exports = {

    setup: function(opts) {
        options = opts;
    },
    fetch: function() {

        NProgress.start();

        return new Promise(function(resolve, reject) {

            /*resolve([{"id":1,"displayname":"B2","description":null,"color":"#E6C800","timezone":"Europe\/Paris"}]);
            NProgress.done();
            return;*/
            
            axios.get(options.apiendpoint + '/calendars')
                .then(function(response) {
                    NProgress.done();
                    resolve(unserializeResponse(response));
                })
                .catch(function (response) {
                    NProgress.done();
                    reject(Error(response));
                });
        });
    }
};