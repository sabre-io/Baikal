/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    
    CalendarStore = require('../stores/CalendarStore'),
    CalendarStateStore = require('../stores/CalendarStateStore'),
    
    CalendarStateActionCreators = require('../actions/CalendarStateActionCreators'),

    CalendarActionCreators = require('../actions/CalendarActionCreators'),
    EventActionCreators = require('../actions/EventActionCreators'),

    Header = require('./Header.react'),
    Surface = require('./Surface.react'),
    SideBar = require('./SideBar.react');

var Calendar = React.createClass({
    mixins: [PureRenderMixin],

    componentDidMount: function() {
        $('html').addClass('cal-week-calendar-view');
        CalendarStateStore.addChangeListener(this.onCalendarStateStoreChange);
    },

    componentWillUnmount: function() {
        $('html').removeClass('cal-week-calendar-view');
        CalendarStateStore.removeChangeListener(this.onCalendarStateStoreChange);
    },

    onCalendarStateStoreChange: function() {
        this.forceUpdate();
    },

    setRangeInState: function(range) {

        CalendarStateActionCreators.changeDateRange(range.start, range.end);

        var calendars = CalendarStore.getAll();

        for(var index in calendars) {
            EventActionCreators.fetch(calendars[index], range);
        }
    },

    previousWeek: function(e) {
        var weekStart = CalendarStateStore.getDateRange().start.clone().subtract(1, 'day').startOf('isoWeek');
        this.setRangeInState({
            start: weekStart,
            end: weekStart.clone().endOf('isoWeek')
        });
    },

    nextWeek: function(e) {
        var weekStart = CalendarStateStore.getDateRange().end.clone().add(1, 'day').startOf('isoWeek');
        this.setRangeInState({
            start: weekStart,
            end: weekStart.clone().endOf('isoWeek')
        });
    },

    thisWeek: function(e) {
        var weekStart = CalendarStateStore.getToday().clone().startOf('isoWeek');
        this.setRangeInState({
            start: weekStart,
            end: weekStart.clone().endOf('isoWeek')
        });
    },

    disableCalendar: function(calendar) {
        CalendarActionCreators.disable(calendar);
    },

    enableCalendar: function(calendar) {
        CalendarActionCreators.enable(calendar);
    },

    displayDay: function(date) {
        var newStartDate = date.clone().startOf('isoWeek');
        if(CalendarStateStore.getDateRange().start.isSame(newStartDate)) {
            return;
        }

        this.setRangeInState({
            start: newStartDate,
            end: newStartDate.clone().endOf('isoWeek')
        });
    },

    render: function() {

        var calendars = CalendarStore.getAll();
        
        return (
            <div className="calendar-app row">
                <div className="col-sm-2">
                    <SideBar
                        calendars={calendars}
                        enableCalendar={this.enableCalendar}
                        disableCalendar={this.disableCalendar}
                        startdate={CalendarStateStore.getDateRange().start}
                        enddate={CalendarStateStore.getDateRange().end}
                        today={CalendarStateStore.getToday()}
                        displayDay={this.displayDay}
                    />
                </div>
                <div className="col-sm-10">
                    <div className="pull-left">
                        <Header startdate={CalendarStateStore.getDateRange().start} enddate={CalendarStateStore.getDateRange().end} />
                    </div>
                    <div className="pull-right">
                        <div className="btn-group cal-week-navigation">
                            <button className="btn btn-default" onClick={this.previousWeek}><i className="fa fa-chevron-left"></i></button>
                            <button className="btn btn-default" onClick={this.thisWeek}>Today</button>
                            <button className="btn btn-default" onClick={this.nextWeek}><i className="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <Surface
                        calendars={calendars}
                        apiendpoint={this.props.apiendpoint}
                        changeRange={this.setRangeInState}
                        hourbarwidth={this.props.hourbarwidth}
                        hourheight={this.props.hourheight}
                        width={this.props.width}
                        starttime={this.props.starttime} endtime={this.props.endtime}
                        startdate={CalendarStateStore.getDateRange().start} enddate={CalendarStateStore.getDateRange().end}
                        businessstarttime={this.props.businessstarttime} businessendtime={this.props.businessendtime}
                        today={CalendarStateStore.getToday()}
                        allowMovingToAdjacentWeeks={this.props.allowMovingToAdjacentWeeks}
                        mouseMoveAddListener={this.props.mouseMoveAddListener}
                        mouseMoveRemoveListener={this.props.mouseMoveRemoveListener}
                        mouseUpAddListener={this.props.mouseUpAddListener}
                        mouseUpRemoveListener={this.props.mouseUpRemoveListener}
                        resizeAddListener={this.props.resizeAddListener}
                        resizeRemoveListener={this.props.resizeRemoveListener}
                        windowed={this.props.windowed}
                        cosmetic={this.props.cosmetic}
                    />
                </div>
            </div>
        );
    }
});

module.exports = Calendar;