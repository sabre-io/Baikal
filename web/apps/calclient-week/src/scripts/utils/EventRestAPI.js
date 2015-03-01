var axios = require('axios'),
    Promise = require('es6-promise').Promise,
    NProgress = require('nprogress');

var options = {};

var unserializeResponse = function(data) {
    return (data && 'data' in data && 'event' in data['data']) ? data['data']['event'] : [];
};

module.exports = {

    setup: function(opts) {
        options = opts;
    },
    fetch: function(calendar, range) {

        NProgress.start();

        return new Promise(function(resolve, reject) {

            /*resolve([
                {"id":301,"title":"Nouvel \u00e9v\u00e9nement","busy":true,"start":"2014-12-31T07:00:00+0100","end":"2014-12-31T10:00:00+0100"},
                {"id":302,"title":"Nouvel \u00e9v\u00e9nement 2","busy":true,"start":"2014-12-31T09:00:00+0100","end":"2014-12-31T11:00:00+0100"}
            ]);
            NProgress.done();
            return;*/
            
            axios.get(options.apiendpoint + '/calendars/' + calendar.id + '/events?start=' + encodeURIComponent(range.start.format()) + '&end=' + encodeURIComponent(range.end.format()))
                .then(function(response) {
                    NProgress.done();
                    resolve(unserializeResponse(response));
                })
                .catch(function (response) {
                    NProgress.done();
                    reject(Error(response));
                });
        });
    },
    updateEvent: function(event) {

        NProgress.start();

        return new Promise(function(resolve, reject) {
            
            axios.put(options.apiendpoint + '/calendars/' + event.calendar.id + '/events' + '/' + event.id, {
                id: event.id,
                title: event.title,
                busy: event.busy,
                start: event.start.format(),
                end: event.end.format()
            })
            .then(function(response) {
                NProgress.done();
                resolve(true);
            })
            .catch(function (response) {
                NProgress.done();
                reject(Error(response));
            });
        });
    },
    createEvent: function(calendar, newEvent) {
        NProgress.start();

        return new Promise(function(resolve, reject) {
            
            axios.post(options.apiendpoint + '/calendars/' + calendar.id  + '/events', {
                title: newEvent.title,
                busy: newEvent.busy,
                start: newEvent.start.format(),
                end: newEvent.end.format()
            })
            .then(function(response) {
                NProgress.done();
                resolve(unserializeResponse(response));
            })
            .catch(function (response) {
                NProgress.done();
                reject(Error(response));
            });
        });
    },
    deleteEvent: function(event) {
        NProgress.start();

        return new Promise(function(resolve, reject) {
            
            axios.delete(options.apiendpoint + '/calendars/' + event.calendar.id + '/events' + '/' + event.id)
            .then(function(response) {
                NProgress.done();
                resolve();
            })
            .catch(function (response) {
                NProgress.done();
                reject(Error(response));
            });
        });
    },
    changeCalendarForEvent: function(event, calendar) {
        NProgress.start();

        return new Promise(function(resolve, reject) {
            
            axios.put(options.apiendpoint + '/calendars/' + event.calendar.id + '/events' + '/' + event.id, {
                id: event.id,
                title: event.title,
                busy: event.busy,
                start: event.start.format(),
                end: event.end.format(),
                calendar: calendar.id
            })
            .then(function(response) {
                NProgress.done();
                resolve(true);
            })
            .catch(function (response) {
                NProgress.done();
                reject(Error(response));
            });
        });
    }
};