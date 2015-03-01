'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    EventEmitter = require('events').EventEmitter,
    CalendarStateConstants = require('../constants/CalendarStateConstants'),
    assign = require('object-assign'),
    moment = require('moment');

var CHANGE_EVENT = 'CHANGE_EVENT';

var stateStore = {
    rangeStart: moment().startOf('isoWeek'),
    rangeEnd: moment().endOf('isoWeek'),
    today: moment().startOf('day'),
    selectedevent: null
};

var CalendarStateStore = assign({}, EventEmitter.prototype, {

    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    },

    changeDateRange: function(start, end) {
        if(start.isSame(stateStore.rangeStart) && end.isSame(stateStore.rangeEnd)) return;

        stateStore.rangeStart = start.clone();
        stateStore.rangeEnd = end.clone();
        this.emit(CHANGE_EVENT);
    },

    getDateRange: function() {
        return {
            start: stateStore.rangeStart.clone(),
            end: stateStore.rangeEnd.clone()
        };
    },

    getToday: function() {
        return stateStore.today.clone();
    },

    selectEvent: function(event) {
        stateStore.selectedevent = event;
        this.emit(CHANGE_EVENT);
    },

    getSelectedEvent: function() {
        return stateStore.selectedevent;
    }
});

// Register to handle all updates
AppDispatcher.register(function(action) {

    switch(action.type) {
        
        case CalendarStateConstants.ActionTypes.CHANGE_DATE_RANGE: {
            CalendarStateStore.changeDateRange(
                action.start,
                action.end
            );
            break;
        }

        case CalendarStateConstants.ActionTypes.SELECT_EVENT: {
            CalendarStateStore.selectEvent(action.event);
            break;
        }
    }

    return true; // No errors.  Needed by promise in Dispatcher.
});

module.exports = CalendarStateStore;