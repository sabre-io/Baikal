'use strict';

var React = require('react/addons'),
    cx = React.addons.classSet,
    Router = require('react-router'),

    keymaster = require('keymaster'),
    moment = require('moment'),

    ColorCheckBox = require('./Misc/ColorCheckBox.react'),
    DaySelector = require('./SideBar/DaySelector.react'),
    TimeSelector = require('./Misc/TimeSelector.react'),
    ColorSelect = require('./Misc/ColorSelect.react'),

    EventStore = require('../stores/EventStore'),
    CalendarStore = require('../stores/CalendarStore'),
    CalendarStateStore = require('../stores/CalendarStateStore'),

    EventActionCreators = require('../actions/EventActionCreators'),
    CalendarActionCreators = require('../actions/CalendarActionCreators'),
    CalendarStateActionCreators = require('../actions/CalendarStateActionCreators');

keymaster.filter = function(event) {
    var tagName = (event.target || event.srcElement).tagName.toUpperCase();
    return tagName === 'INPUT';
}

var Edit = React.createClass({
    mixins: [Router.State, Router.Navigation],
    getInitialState: function() {
        
        var event = EventStore.get(this.getParams().eventid);
        if(!event) this.backToCalendar();

        var dateformat = 'YYYY-MM-DD';

        return {
            dateformat: dateformat,
            event: event,

            startTxt: event.start.clone().format(dateformat),
            endTxt: event.end.clone().format(dateformat),

            startTimeTxt: event.start.clone().format('HH:mm'),
            endTimeTxt: event.end.clone().format('HH:mm'),

            startHighlighted: false,
            endHighlighted: false,

            allday: (
                event.start.clone().startOf('day').isSame(event.start) &&
                event.end.clone().startOf('day').isSame(event.end)
            ),

            augmentedWhenAllDay: false,
            timeStartBeforeAllDay_hour: 0,
            timeStartBeforeAllDay_minute: 0,
            timeEndBeforeAllDay_hour: 0,
            timeEndBeforeAllDay_minute: 0,

            errorStart: false,
            errorEnd: false,

            hotCalendarId: event.calendar.id,

            hotProps: {
                title: event.title,
                start: event.start.clone(),
                end: event.end.clone()
            }
        };
    },
    componentDidMount: function() {
        $('html').addClass('cal-week-edit-view');
        keymaster('enter', this.handleKeyEnter);
    },
    componentWillUnmount: function() {
        $('html').removeClass('cal-week-edit-view');
        keymaster.unbind('enter');
    },
    backToCalendar: function() {
        this.transitionTo('calendar');
    },

    handleKeyEnter: function(e) {
        this.handleSave();
    },
    handleInputDblClick: function(e) {
        e.target.setSelectionRange(0, e.target.value.length);
    },
    handleTitleChange: function(e) {
        var hotProps = this.state.hotProps;
        hotProps.title = e.target.value;
        this.setState({hotProps: hotProps});
    },
    handleSave: function() {

        if(this.hasError()) return;

        var hotProps = this.state.hotProps;

        if(this.state.allday) {
            // if allday checked, aligning times on midnight on both ends
            hotProps.start.startOf('day');
        }

        var saveEvent = function(event) {
            EventActionCreators.updateSomePropsEvent(
                event,
                hotProps
            );
        };

        if(this.state.hotCalendarId !== this.state.event.calendar.id) {
            //this.state.event.calendar = CalendarStore.get(this.state.hotCalendarId);

            EventActionCreators.updateSomePropsEventWithoutPersiting(
                this.state.event,
                hotProps
            );

            EventActionCreators.changeCalendar(
                this.state.event,
                CalendarStore.get(this.state.hotCalendarId)
            );
        } else {
            saveEvent(this.state.event);
        }

        // Displaying event week in calendar
        CalendarStateActionCreators.changeDateRange(
            hotProps.start.clone().startOf('isoWeek'),
            hotProps.start.clone().endOf('isoWeek')
        );

        // Enabling the calendar holding the event
        CalendarActionCreators.enable(this.state.event.calendar);

        this.backToCalendar();
    },
    handleStartDaySelection: function(selecteddate) {
        var hotProps = this.state.hotProps;

        // the duration remains constant
        // we change the end date
        var duration = hotProps.end.diff(hotProps.start);

        var newStart = selecteddate.clone().hour(hotProps.start.hour()).minute(hotProps.start.minute());
        var newEnd = newStart.clone().add(duration, 'millisecond');

        hotProps.start = newStart;
        hotProps.end = newEnd;
        this.setState({
            errorStart: false,
            startTxt: hotProps.start.format(this.state.dateformat),
            endTxt: hotProps.end.format(this.state.dateformat),
            hotProps: hotProps,
        });
    },

    handleEndDaySelection: function(selecteddate) {
        var hotProps = this.state.hotProps;

        if(hotProps.end.clone().startOf('day').isSame(hotProps.end)) {
            selecteddate.add(1, 'day');
        }

        hotProps.end = hotProps.end.clone().year(selecteddate.year()).month(selecteddate.month()).date(selecteddate.date());
        this.setState({
            errorEnd: false,
            endTxt: hotProps.end.format(this.state.dateformat),
            hotProps: hotProps
        });
    },

    handleAllDayClick: function(checked) {

        var newState = { allday: checked };
        var hotProps = this.state.hotProps;
        
        if(newState.allday) {

            newState.timeStartBeforeAllDay_hour = hotProps.start.hour();
            newState.timeStartBeforeAllDay_minute = hotProps.start.minute();
            newState.timeEndBeforeAllDay_hour = hotProps.end.hour();
            newState.timeEndBeforeAllDay_minute = hotProps.end.minute();

            hotProps.start.startOf('day');
            newState.startTxt = hotProps.start.format(this.state.dateformat);

            if(!hotProps.end.clone().startOf('day').isSame(hotProps.end)) {
                
                newState.augmentedWhenAllDay = true;
                hotProps.end.startOf('day').add(1, 'day');
                newState.endTxt = hotProps.end.format(this.state.dateformat);
            }
        } else {
            newState.augmentedWhenAllDay = false;

            if(this.state.augmentedWhenAllDay) {
                hotProps.end.subtract(1, 'day');
            }

            hotProps.start.hour(this.state.timeStartBeforeAllDay_hour).minute(this.state.timeStartBeforeAllDay_minute);
            hotProps.end.hour(this.state.timeEndBeforeAllDay_hour).minute(this.state.timeEndBeforeAllDay_minute);
            newState.startTxt = hotProps.start.format(this.state.dateformat);
            newState.endTxt = hotProps.end.format(this.state.dateformat);
        }

        newState.hotProps = hotProps;
        this.setState(newState);
    },

    handleDaySelectionEnter: function(what, e) {
        var newState = {};
        newState[what + 'Highlighted'] = true;
        this.setState(newState);
    },

    handleDaySelectionLeave: function(what, e) {
        var newState = {};
        newState[what + 'Highlighted'] = false;
        this.setState(newState);
    },

    handleDelete: function(e) {
        EventActionCreators.deleteEvent(this.state.event);
        this.backToCalendar();
    },

    hasError: function() {
        return this.state.errorStart || this.state.errorEnd;
    },

    handleCalendarChange: function(calendar) {
        this.setState({hotCalendarId: calendar.id});
    },

    render: function() {

        var self = this;
        var today = CalendarStateStore.getToday();

        var startDateChange = function(e) {

            var hotProps = self.state.hotProps;
            var newState = { startTxt: e.target.value },
                newDate = moment(newState.startTxt, self.state.dateformat);

            if(newDate.isValid()) {
                // the duration remains constant
                // we change the end date
                var duration = hotProps.end.diff(hotProps.start);

                var newStart = newDate.clone().hour(hotProps.start.hour()).minute(hotProps.start.minute());
                var newEnd = newStart.clone().add(duration, 'millisecond');

                hotProps.start = newStart;
                hotProps.end = newEnd;

                newState.errorStart = false;
                newState.endTxt = hotProps.end.format(self.state.dateformat);
                newState.hotProps = hotProps;

            } else {
                newState.errorStart = true;
            }

            self.setState(newState);
        };

        var endDateChange = function(e) {

            var newState = { endTxt: e.target.value };
            var newDate = moment(newState.endTxt, self.state.dateformat);

            if(newDate.isValid()) {

                if(newDate.isBefore(self.state.hotProps.start, 'day')) {
                    newState.errorEnd = true;
                } else {

                    var hotProps = self.state.hotProps;
                    var parsedStart = moment(self.state.startTxt, self.state.dateformat);

                    if(parsedStart.isValid()) {
                        hotProps.start = parsedStart;
                        newState.errorStart = false;
                    }

                    newState.errorEnd = false;

                    hotProps.end = hotProps.end.clone().year(newDate.year()).month(newDate.month()).date(newDate.date());
                    newState.hotProps = hotProps;
                }
            } else {
                newState.errorEnd = true;
            }

            self.setState(newState);
        };

        var startTimeChange = function(hour, minute) {
            _timeChange('start', hour, minute);
        };

        var endTimeChange = function(hour, minute) {
            _timeChange('end', hour, minute);
        };

        var _timeChange = function(what, hour, minute) {
            var hotProps = self.state.hotProps;
            hotProps[what].hour(hour).minute(minute);
            self.setState({hotProps: hotProps});
        };

        var txtStartStyle = { },
            txtEndStyle = { };

        if(this.state.errorStart) {
            txtStartStyle.backgroundColor = 'red';
            txtStartStyle.color = 'white';
        }

        if(this.state.errorEnd) {
            txtEndStyle.backgroundColor = 'red';
            txtEndStyle.color = 'white';
        }

        // Should we display the calendar selector ?
        var displayCalendarSelector = (CalendarStore.count() > 1),
            cals = displayCalendarSelector ? CalendarStore.getAll() : {},
            calendars = [];

        for(var calendarIndex in cals) {
            calendars.push(cals[calendarIndex]);
        }

        return (
            <div>
                <div className="row">
                    <div className="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
                        <div className="row">
                            <div className="col-xs-12" style={{marginTop: '3px'}}>
                                <button onClick={this.backToCalendar} className="btn btn-default"><i className="fa fa-level-up fa-rotate-270"></i> Back</button>
                                
                                &nbsp;&nbsp;&nbsp;
                                
                                <button className={cx({
                                    "btn": true,
                                    "btn-primary": true,
                                    'disabled': this.hasError()
                                })} onClick={this.handleSave}><i className="fa fa-save"></i> Save</button>
                                &nbsp;&nbsp;&nbsp;

                                <button className="btn btn-danger" onClick={this.handleDelete}><i className="fa fa-trash"></i> Delete</button>
                                <p className="visible-xs">&nbsp;</p>
                            </div>
                        </div>

                        <hr />

                        <div className="editform">
                        
                            <div className="row">
                                <div className="col-xs-12">
                                    <input type="text" className="form-control input-lg" style={{fontWeight: 'bold'}} value={this.state.hotProps.title} onChange={this.handleTitleChange} onDblClick={this.handleInputDblClick} />
                                </div>
                            </div>

                            <hr />
                            
                            { displayCalendarSelector && <ColorSelect
                                items={calendars}
                                selected={CalendarStore.get(this.state.hotCalendarId)}
                                itemvalue={function(cal) { return cal.id; }}
                                itemlabel={function(cal) {
                                    return (<div style={{display: 'inline-block'}}><div style={{width: '14px', 'height': '14px', display: 'inline-block', backgroundColor: cal.darkColor, position: 'relative', 'top': '2px'}}></div> <span>{cal.displayname}</span></div>);
                                }}
                                onChange={this.handleCalendarChange}
                            />}

                            <hr />

                            <div className="row">
                                <div className="col-xs-12">
                                    <ColorCheckBox checked={this.state.allday} onChange={this.handleAllDayClick} uncheckedColor='#DDD' /> All day
                                </div>
                            </div>

                            <hr />

                            <div className="row">
                                <div className="col-sm-6 col-xs-12">
                                    <h4>From</h4>

                                    <div className="row">
                                        <div className="col-sm-8">
                                            <input type="text" className={cx({
                                                'form-control': true,
                                                'input-sm': true,
                                                'day-input-focused': this.state.startHighlighted
                                            })} value={this.state.startTxt} onChange={startDateChange} style={txtStartStyle} />
                                        </div>
                                        <div className="col-sm-4">
                                            {!this.state.allday && (<TimeSelector
                                                datetime={this.state.hotProps.start.clone()}
                                                timeChanged={startTimeChange}
                                            />)}
                                            {this.state.allday && (<input type="text" className="form-control input-sm" disabled value="00:00" />)}
                                        </div>
                                    </div>

                                    <div onMouseEnter={this.handleDaySelectionEnter.bind(this, 'start')} onMouseLeave={this.handleDaySelectionLeave.bind(this, 'start')}>
                                        <DaySelector
                                            startdate={this.state.hotProps.start.clone().startOf('month')}
                                            enddate={this.state.hotProps.start.clone().endOf('month')}
                                            selectedDay={this.state.hotProps.start.clone()}
                                            selectDay={this.handleStartDaySelection}
                                            today={today}
                                            
                                            rangeBound={this.state.hotProps.end.clone()}
                                            className={['start']} />
                                    </div>
                                </div>

                                <div className="col-sm-6 col-xs-12">

                                    <h4>To</h4>

                                    <div className="row">
                                        <div className="col-sm-8">
                                            <input type="text" className={cx({
                                                'form-control': true,
                                                'input-sm': true,
                                                'day-input-focused': this.state.endHighlighted
                                            })} value={this.state.endTxt} onChange={endDateChange} style={txtEndStyle} />
                                        </div>
                                        <div className="col-sm-4">
                                            {!this.state.allday && (<TimeSelector
                                                datetime={this.state.hotProps.end.clone()}
                                                timeChanged={endTimeChange}
                                            />)}
                                            {this.state.allday && (<input type="text" className="form-control input-sm" disabled value="00:00" />)}
                                        </div>
                                    </div>

                                    <div onMouseEnter={this.handleDaySelectionEnter.bind(this, 'end')} onMouseLeave={this.handleDaySelectionLeave.bind(this, 'end')}>
                                        <DaySelector
                                            startdate={this.state.hotProps.end.clone().startOf('month')}
                                            enddate={this.state.hotProps.end.clone().endOf('month')}
                                            selectedDay={this.state.hotProps.end.clone()}
                                            selectDay={this.handleEndDaySelection}
                                            today={today}
                                            disableBefore={this.state.hotProps.start.clone()}
                                            rangeBound={this.state.hotProps.start.clone()}
                                            className={['end']} />
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
        );
    }
});

module.exports = Edit;