var AppDispatcher = require('../dispatcher/AppDispatcher'),
    EventConstants = require('../constants/EventConstants'),
    ActionTypes = EventConstants.ActionTypes;

module.exports = {

    fetch: function(calendar, range) {

        // broadening the range to pre-fetch events
        var broadRange = {
            start: range.start.clone().subtract(1, 'week'),
            end: range.end.clone().add(1, 'week')
        };

        AppDispatcher.dispatch({
            type: ActionTypes.FETCH_CALENDAR_EVENTS,
            calendar: calendar,
            range: broadRange
        });
    },

    receiveCalendar: function(calendar, events) {
        AppDispatcher.dispatch({
            type: ActionTypes.RECEIVE_CALENDAR_EVENTS,
            calendar: calendar,
            events: events
        });
    },

    displaceEvent: function(event, projecteddate) {
        AppDispatcher.dispatch({
            type: ActionTypes.DISPLACE_EVENT,
            event: event,
            projecteddate: projecteddate
        });
    },

    displacedEvent: function(event) {
        AppDispatcher.dispatch({
            type: ActionTypes.DISPLACED_EVENT,
            event: event
        });
    },

    resizeEvent: function(event, projecteddate) {
        AppDispatcher.dispatch({
            type: ActionTypes.RESIZE_EVENT,
            event: event,
            projecteddate: projecteddate
        });
    },

    resizedEvent: function(event) {
        AppDispatcher.dispatch({
            type: ActionTypes.RESIZED_EVENT,
            event: event
        });
    },

    updateSomePropsEvent: function(event, changedprops) {
        AppDispatcher.dispatch({
            type: ActionTypes.UPDATESOMEPROPS_EVENT,
            event: event,
            changedprops: changedprops
        });
    },

    updateSomePropsEventWithoutPersiting: function(event, changedprops) {
        AppDispatcher.dispatch({
            type: ActionTypes.UPDATESOMEPROPS_EVENT,
            event: event,
            changedprops: changedprops,
            nopersist: true
        });
    },

    createEvent: function(calendar, eventprops) {
        AppDispatcher.dispatch({
            type: ActionTypes.CREATE_EVENT,
            calendar: calendar,
            eventprops: eventprops
        });
    },

    deleteEvent: function(event) {
        AppDispatcher.dispatch({
            type: ActionTypes.DELETE_EVENT,
            event: event
        });
    },

    changeCalendar: function(event, calendar) {

        AppDispatcher.dispatch({
            type: ActionTypes.CHANGE_CALENDAR,
            event: event,
            calendar: calendar
        });
    }
};