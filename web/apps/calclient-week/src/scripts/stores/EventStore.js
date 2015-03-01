'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    EventEmitter = require('events').EventEmitter,
    ActionTypes = require('../constants/EventConstants').ActionTypes,
    assign = require('object-assign'),

    moment = require('moment'),
    EventActionCreators = require('../actions/EventActionCreators'),
    EventRestAPI = require('../utils/EventRestAPI'),
    MomentSelector = require('../utils/MomentSelector'),
    RangeStore = require('../utils/RangeStore');

var CHANGE_EVENT = 'change';
var storeData = {};

var EventStore = assign({}, EventEmitter.prototype, {

    setup: function(options) {
        EventRestAPI.setup(options);
    },

    getAll: function() {
        return storeData;
    },

    get: function(id) {
        if(!storeData[id]) return null;

        return storeData[id];
    },

    getDay: function(day) {

        var res = [],
            daystart = day.clone().startOf('day'),
            dayend = day.clone().endOf('day');

        for(var id in storeData) {
            if(
                (storeData[id].calendar.disabled === false) &&
                MomentSelector.overlaps(daystart, dayend, storeData[id].start, storeData[id].end)
            ) {
                res.push(storeData[id]);
            }
        }

        return res;
    },

    emitChange: function() {
        this.emit(CHANGE_EVENT);
    },

    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    },

    receive: function(calendar, events) {
        for(var i in events) {

            var eventKey = events[i].id;
            if(!storeData[eventKey]) {
                storeData[eventKey] = events[i];
                storeData[eventKey].start = moment(events[i].start);
                storeData[eventKey].end = moment(events[i].end);
                storeData[eventKey].calendar = calendar;
            }
        }

        this.emitChange();
    },
    inject: function(event) {
        this.receive(event.calendar, [event]);
    },
    eject: function(event) {
        var eventKey = event.id;
        if(storeData[eventKey]) {
            delete storeData[eventKey];
            this.emitChange();
        }
    }
});

var rangeStores = {};

// Register to handle all updates
AppDispatcher.register(function(action) {

    switch(action.type) {
        case ActionTypes.FETCH_CALENDAR_EVENTS: {

            if(!rangeStores[action.calendar.id]) {
                rangeStores[action.calendar.id] = new RangeStore();
            }

            var rangeStore = rangeStores[action.calendar.id];

            if(!rangeStore.isRangeFetched(action.range)) {
                EventRestAPI.fetch(action.calendar, action.range).then(function(events) {
                    EventActionCreators.receiveCalendar(
                        action.calendar,
                        events
                    );
                });

                rangeStore.aggregateRange(action.range);
            } else {
                // range is already fetched; nothing to do !
            }

            break;
        }
        case ActionTypes.RECEIVE_CALENDAR_EVENTS: {

            EventStore.receive(
                action.calendar,
                action.events
            );

            break;
        }
        case ActionTypes.DISPLACE_EVENT: {

            // This is all handled clientside; persistence is handled by DISPLACED_EVENT below

            var durationInMs = action.event.end.diff(action.event.start);
            action.event.start = action.projecteddate;
            action.event.end = action.projecteddate.clone().add(durationInMs, 'milliseconds');
            EventStore.emitChange();

            break;
        }
        case ActionTypes.DISPLACED_EVENT: {

            EventRestAPI.updateEvent(action.event).then(function(success) {});

            break;
        }
        case ActionTypes.RESIZE_EVENT: {
            
            action.event.end = action.projecteddate.clone();
            EventStore.emitChange();

            break;
        }
        case ActionTypes.RESIZED_EVENT: {

            //console.log('Resizing event to', action.event.end);
            EventRestAPI.updateEvent(action.event).then(function(success) {});

            break;
        }

        case ActionTypes.UPDATESOMEPROPS_EVENT: {
            
            for(var propname in action.changedprops) {
                action.event[propname] = action.changedprops[propname];
            }

            EventStore.emitChange();

            if(!action.nopersist) {
                EventRestAPI.updateEvent(action.event).then(function(success) {});
            }

            break;
        }

        case ActionTypes.CREATE_EVENT: {
            //console.log('CREATE EVENT !', action);
            
            var temporaryEvent = action.eventprops;    // clone array
            temporaryEvent.id = 'temporary-' + (Math.random() * 1000000);
            temporaryEvent.temporary = true;
            temporaryEvent.calendar = action.calendar;
            EventStore.inject(temporaryEvent);

            EventRestAPI.createEvent(action.calendar, action.eventprops).then(function(event) {
                EventStore.eject(temporaryEvent);
                EventStore.receive(
                    action.calendar,
                    [event]
                );
            });

            break;
        }
        case ActionTypes.DELETE_EVENT: {
            //console.log('DELETE EVENT !', action);

            EventStore.eject(action.event);
            EventRestAPI.deleteEvent(action.event).then(function() {});

            break;
        }

        case ActionTypes.CHANGE_CALENDAR: {

            EventRestAPI.changeCalendarForEvent(action.event, action.calendar).then(function() {});
            action.event.calendar = action.calendar;
            EventStore.emitChange();

            break;
        }
    }

    return true; // No errors.  Needed by promise in Dispatcher.
});

module.exports = EventStore;