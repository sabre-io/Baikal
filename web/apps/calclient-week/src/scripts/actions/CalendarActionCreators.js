'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    CalendarActionTypes = require('../constants/CalendarConstants').ActionTypes,
    CalendarRestAPI = require('../utils/CalendarRestAPI'),
    EventActionCreators = require('./EventActionCreators'),
    moment = require('moment');

var CalendarActionCreators = {

    fetch: function() {
        var self = this;

        AppDispatcher.dispatch({
            type: CalendarActionTypes.FETCH_CALENDARS
        });

        CalendarRestAPI.fetch().then(function(calendars) {
            self.receive(calendars);
        });
    },

    receive: function(calendars) {
        AppDispatcher.dispatch({
            type: CalendarActionTypes.RECEIVE_CALENDARS,
            calendars: calendars
        });

        // Fetching the current week for each calendar
        var range = {
            start: moment().startOf('isoWeek'),
            end: moment().endOf('isoWeek')
        };

        for(var calendarIndex in calendars) {
            EventActionCreators.fetch(calendars[calendarIndex], range);
        }
    },

    disable: function(calendar) {
        AppDispatcher.dispatch({
            type: CalendarActionTypes.DISABLE_CALENDAR,
            calendar: calendar
        });
    },

    enable: function(calendar) {
        AppDispatcher.dispatch({
            type: CalendarActionTypes.ENABLE_CALENDAR,
            calendar: calendar
        });
    }
};

module.exports = CalendarActionCreators;