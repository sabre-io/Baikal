/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    //PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet,
    ColorCheckBox = require('../Misc/ColorCheckBox.react'),

    AppStateActionCreators = require('../../actions/AppStateActionCreators'),
    AppStateStore = require('../../stores/AppStateStore');

var CalendarSelector = React.createClass({
    //mixins: [PureRenderMixin],    // Not PureRender because react cannot detect changes in array props (this.props.calendars)

    getInitialState: function() {
        return {};
    },

    componentDidMount: function() {
        AppStateStore.addFocusedCalendarChangeListener(this.onFocusedCalendarChange);
    },

    componentWillUnmount: function() {
        AppStateStore.removeFocusedCalendarChangeListener(this.onFocusedCalendarChange);
    },

    onFocusedCalendarChange: function() {
        this.forceUpdate();
    },

    render: function() {

        var calendarList = [],
            self = this,
            focusedCalendar = AppStateStore.getFocusedCalendar();

        var onCalendarToggle = function(calendar, checked) {

            if(checked) {
                self.props.enableCalendar(calendar);
            } else {
                self.props.disableCalendar(calendar);
            }
        };

        var handleCalendarFocus = function(calendar) {
            AppStateActionCreators.focusCalendar(calendar);
        };

        for(var calIndex in this.props.calendars) {
            var calendar = this.props.calendars[calIndex];

            var checked = (calendar.disabled === false),
                focused = (focusedCalendar === calendar);

            var style = {
                color: calendar.lightColorText
            };

            if(focused) {
                style.backgroundColor = calendar.lightColor;
            }

            // <input type="checkbox" onChange={onCalendarToggle.bind(this, calendar)} checked={checked} />

            calendarList.push(
                <div key={calendar.id} style={style} className={cx({
                    'calendar': true,
                    'calendar-focused': focused
                })}>
                    <ColorCheckBox checked={checked} onChange={onCalendarToggle.bind(this, calendar)} color={calendar.darkColor} />

                    <span className="calendar-title" onClick={handleCalendarFocus.bind(this, calendar)}>
                        {calendar.displayname}
                    </span>
                </div>
            );
        }

        return (
            <div className="calendar-selector">
                {calendarList}
            </div>
        );
    }
});

module.exports = CalendarSelector;