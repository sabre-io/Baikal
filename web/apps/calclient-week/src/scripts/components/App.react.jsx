/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    RouteHandler = require('react-router').RouteHandler,

    CalendarStore = require('../stores/CalendarStore'),
    AppStateStore = require('../stores/AppStateStore'),
    CalendarActionCreators = require('../actions/CalendarActionCreators');

var mousemoveListeners = {},
    mouseupListeners = {},
    resizeListeners = {};

var App = React.createClass({
    getInitialState: function() {
        return {
            width: null
        };
    },
    onResize: function(e) {
        
        var width = $(this.getDOMNode()).width();
        if(width !== this.state.width) {
            // calendar width may be constrained to a CSS grid, thus it's width may remain the same when the window resizes
            this.setState({width: width});
        }

        for(var resizeListenerToken in resizeListeners) {
            resizeListeners[resizeListenerToken](e);
        }
    },

    onMouseMove: function(e) {
        for(var mousemoveListenerToken in mousemoveListeners) {
            mousemoveListeners[mousemoveListenerToken](e);
        }
    },

    onMouseUp: function(e) {
        for(var mouseupListenerToken in mouseupListeners) {
            mouseupListeners[mouseupListenerToken](e);
        }
    },

    componentDidMount: function() {
        
        var self = this;

        this.onResize();
        
        $(window).on('resize', this.onResize);
        $(window).on('mousemove', this.onMouseMove);
        $(window).on('mouseup', this.onMouseUp);

        CalendarStore.setup({
            apiendpoint: this.props.apiendpoint
        });
        CalendarStore.addChangeListener(this.onCalendarStoreChange);
        CalendarStore.addReceiveListener(this.onCalendarStoreReceive);
        CalendarActionCreators.fetch();

        AppStateStore.addEditedEventChangeListener(function() {
            self.forceUpdate();
        });
    },

    componentWillUnmount: function() {
        $(window).off('resize', this.onResize);
        $(window).off('mousemove', this.onMouseMove);
        $(window).off('mouseup', this.onMouseUp);

        CalendarStore.removeChangeListener(this.onCalendarStoreChange);
        CalendarStore.removeReceiveListener(this.onCalendarStoreReceive);
    },

    onCalendarStoreChange: function() {
        this.forceUpdate();
    },

    onCalendarStoreReceive: function() {

        // Focusing the first calendar received
        var calendars = CalendarStore.getAll();
        if(calendars.length === 0) {
            this.forceUpdate();
            return;
        }
        
        // Not using the AppStateActionCreators to avoid dispatch during dispatch
        //AppStateActionCreators.focusCalendar(calendars[Object.keys(calendars)[0]]);
        var calendarToFocus = null;

        if(this.props.calendarFocusedAtStart) {
            var calendarToFocus = CalendarStore.get(this.props.calendarFocusedAtStart);
        }

        if(
            this.props.calendarsEnabledAtStart &&
            this.props.calendarsEnabledAtStart instanceof Array &&
            this.props.calendarsEnabledAtStart.length > 0
        ) {
            for(var calendarIndex in calendars) {
                if(this.props.calendarsEnabledAtStart.indexOf(calendars[calendarIndex].id) === -1) {
                    calendars[calendarIndex].disabled = true;
                }
            }
        }

        if(!calendarToFocus) calendarToFocus = calendars[Object.keys(calendars)[0]];
        AppStateStore.focusCalendar(calendarToFocus);
    },

    render: function() {

        var addListener = function(cbk, listenersStore) {
            var token = (new Date()).getTime() + '-' + parseInt(Math.random() * 100000);
            listenersStore[token] = cbk;
            return token;
        };

        var removeListener = function(token, listenersStore) {
            if(!listenersStore[token]) return false;
            
            delete listenersStore[token];
            return true;
        };

        var mouseMoveAddListener = function(cbk) { return addListener(cbk, mousemoveListeners); };
        var mouseMoveRemoveListener = function(token) { return removeListener(token, mousemoveListeners); };
        var mouseUpAddListener = function(cbk) { return addListener(cbk, mouseupListeners); };
        var mouseUpRemoveListener = function(token) { return removeListener(token, mouseupListeners); };
        var resizeAddListener = function(token) { return addListener(token, resizeListeners); };
        var resizeRemoveListener = function(token) { return removeListener(token, resizeListeners); };

        return (
            <div className="container cal-week">
                <RouteHandler
                    {...this.props}
                    mouseMoveAddListener={mouseMoveAddListener}
                    mouseMoveRemoveListener={mouseMoveRemoveListener}
                    mouseUpAddListener={mouseUpAddListener}
                    mouseUpRemoveListener={mouseUpRemoveListener}
                    resizeAddListener={resizeAddListener}
                    resizeRemoveListener={resizeRemoveListener} />
            </div>
        );
    }
});

module.exports = App;