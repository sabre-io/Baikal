'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    EventEmitter = require('events').EventEmitter,
    ActionTypes = require('../constants/CalendarConstants').ActionTypes,
    assign = require('object-assign'),
    
    CalendarRestAPI = require('../utils/CalendarRestAPI'),
    Color = require('color');

var CHANGE_EVENT = 'CHANGE_EVENT',
    RECEIVE_EVENT = 'RECEIVE_EVENT';

var storeData = {};

var CalendarStore = assign({}, EventEmitter.prototype, {

    setup: function(options) {
        CalendarRestAPI.setup(options);
    },

    getAll: function() {
        return storeData;
    },

    get: function(id) {
        if(!storeData[id]) return null;

        return storeData[id];
    },

    count: function() {
        return Object.keys(storeData).length;
    },

    emitChange: function() {
        this.emit(CHANGE_EVENT);
    },

    emitReceive: function() {
        this.emit(RECEIVE_EVENT);
    },

    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    },

    addReceiveListener: function(callback) {
        this.on(RECEIVE_EVENT, callback);
    },

    removeReceiveListener: function(callback) {
        this.removeListener(RECEIVE_EVENT, callback);
    },

    receive: function(calendars) {
        
        for(var i in calendars) {
            if(!storeData[calendars[i].id]) {
                storeData[calendars[i].id] = calendars[i];

                // resolving colors for all events of this calendar

                var calendarColor = Color(calendars[i].color),
                    lightColor = calendarColor.clone().desaturate(0.2).lighten(0.6).clearer(0.3),
                    darkColor = calendarColor.clone().clearer(0.1);

                var lightColorText = calendarColor.clone().darken(0.2).rgbString(),
                    darkColorText = (darkColor.dark() ? '#FFF' : '#000');

                storeData[calendars[i].id].lightColor = lightColor.rgbString();
                storeData[calendars[i].id].darkColor = darkColor.rgbString();
                storeData[calendars[i].id].lightColorText = lightColorText;
                storeData[calendars[i].id].darkColorText = darkColorText;
                storeData[calendars[i].id].disabled = false;
            }
        }
    }
});

// Register to handle all updates
AppDispatcher.register(function(action) {

    switch(action.type) {
        case ActionTypes.FETCH_CALENDARS: {
            /*
                nothing; everything is handled in action, to keep the store synchronous, thus avoiding the dreaded
                "Invariant Violation: Dispatch.dispatch(...): Cannot dispatch in the middle of a dispatch."
                thrown when dispatching during a dispatch
            */
            break;
        }
        case ActionTypes.RECEIVE_CALENDARS: {
            CalendarStore.receive(action.calendars);
            CalendarStore.emitReceive();
            break;
        }
        case ActionTypes.DISABLE_CALENDAR: {

            if(action.calendar.disabled === false) {
                action.calendar.disabled = true;
                CalendarStore.emitChange();
            }

            break;
        }
        case ActionTypes.ENABLE_CALENDAR: {
            
            if(action.calendar.disabled === true) {
                action.calendar.disabled = false;
                CalendarStore.emitChange();
            }

            break;
        }
    }

    return true; // No errors.  Needed by promise in Dispatcher.
});

module.exports = CalendarStore;