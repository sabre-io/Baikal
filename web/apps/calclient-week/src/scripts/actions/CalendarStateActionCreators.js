'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    CalendarStateConstants = require('../constants/CalendarStateConstants');

module.exports = {
    changeDateRange: function(start, end) {
        AppDispatcher.dispatch({
            type: CalendarStateConstants.ActionTypes.CHANGE_DATE_RANGE,
            start: start.clone(),
            end: end.clone()
        });
    },

    selectEvent: function(event) {
        AppDispatcher.dispatch({
            type: CalendarStateConstants.ActionTypes.SELECT_EVENT,
            event: event
        });
    }
};