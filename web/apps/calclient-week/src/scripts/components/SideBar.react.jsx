'use strict';

var React = require('react/addons'),
    //PureRenderMixin = React.addons.PureRenderMixin,

    CalendarSelector = require('./SideBar/CalendarSelector.react'),
    DaySelector = require('./SideBar/DaySelector.react');

var SideBar = React.createClass({
    //mixins: [PureRenderMixin],    // Not PureRender because react cannot detect changes in array props (this.props.calendars)

    getInitialState: function() {
        return {};
    },

    render: function() {

        return (
            <div className="cal-week-sidebar">
                <DaySelector
                    startdate={this.props.startdate}
                    enddate={this.props.enddate}
                    today={this.props.today}
                    selectDay={this.props.displayDay}
                    highlightSelectedDayWeek={true} />
                <CalendarSelector
                    calendars={this.props.calendars}
                    enableCalendar={this.props.enableCalendar}
                    disableCalendar={this.props.disableCalendar} />
            </div>
        );
    }
});

module.exports = SideBar;