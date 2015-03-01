'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    EventEmitter = require('events').EventEmitter,
    ActionTypes = require('../constants/AppStateConstants').ActionTypes,
    assign = require('object-assign');

var FOCUSEDCALENDARCHANGE_EVENT = 'FOCUSEDCALENDARCHANGE_EVENT',
    EDITEDEVENTCHANGE_EVENT = 'EDITEDEVENTCHANGE_EVENT';

var stateStore = {
    focusedCalendar: null,
    editedEvent: null
};

var AppStateStore = assign({}, EventEmitter.prototype, {

    initialize: function(state) {
        stateStore = state;
    },

    addFocusedCalendarChangeListener: function(callback) {
        this.on(FOCUSEDCALENDARCHANGE_EVENT, callback);
    },

    removeFocusedCalendarChangeListener: function(callback) {
        this.removeListener(FOCUSEDCALENDARCHANGE_EVENT, callback);
    },

    focusCalendar: function(calendar) {
        stateStore.focusedCalendar = calendar;
        this.emit(FOCUSEDCALENDARCHANGE_EVENT);
    },

    getFocusedCalendar: function() {
        return stateStore.focusedCalendar;
    },


    addEditedEventChangeListener: function(callback) {
        this.on(EDITEDEVENTCHANGE_EVENT, callback);
    },

    removeEditedEventChangeListener: function(callback) {
        this.removeListener(EDITEDEVENTCHANGE_EVENT, callback);
    },

    editEvent: function(event) {
        stateStore.editedEvent = event;
        this.emit(EDITEDEVENTCHANGE_EVENT);
    },

    getEditedEvent: function() {
        return stateStore.editedEvent;
    },
});

// Register to handle all updates
AppDispatcher.register(function(action) {

    switch(action.type) {
        case ActionTypes.FOCUS_CALENDAR: {
            AppStateStore.focusCalendar(action.calendar);
            break;
        }
    }

    return true; // No errors.  Needed by promise in Dispatcher.
});

module.exports = AppStateStore;