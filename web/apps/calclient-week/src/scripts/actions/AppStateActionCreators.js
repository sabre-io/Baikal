'use strict';

var AppDispatcher = require('../dispatcher/AppDispatcher'),
    AppStateConstants = require('../constants/AppStateConstants'),
    CalendarActionCreators = require('../actions/CalendarActionCreators');

module.exports = {
    focusCalendar: function(calendar) {
        AppDispatcher.dispatch({
            type: AppStateConstants.ActionTypes.FOCUS_CALENDAR,
            calendar: calendar
        });

        if(calendar.disabled === true) {
            CalendarActionCreators.enable(calendar);
        }
    }
};