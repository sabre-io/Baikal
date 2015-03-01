/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    Navigation = require('react-router').Navigation,
    //PureRenderMixin = React.addons.PureRenderMixin,

    ArrowPopup = require('../Misc/ArrowPopup.react'),
    ColorSelect = require('../Misc/ColorSelect.react'),

    CalendarStore = require('../../stores/CalendarStore'),

    EventActionCreators = require('../../actions/EventActionCreators'),
    CalendarActionCreators = require('../../actions/CalendarActionCreators'),

    StringUtils = require('../../utils/StringUtils'),
    DateTimeUtils = require('../../utils/DateTimeUtils');

var DetailPopup = React.createClass({
    //mixins: [PureRenderMixin],    // not puremixin because react cannot detect event property changes (startdate and enddate, notably)
    mixins: [Navigation],
    getInitialState: function() {
        return {
            hottitle: this.props.event.title,
            origtitle: this.props.event.title,
            hasChanged: false
        };
    },
    handleChange: function(e) {
        var newState = {hottitle: e.target.value};
        
        if(this.state.origtitle !== e.target.value) {
            newState.hasChanged = true;
        } else {
            newState.hasChanged = false;
        }
        
        this.setState(newState);
    },
    handleEdit: function(e) {
        this.transitionTo('edit', {eventid: this.props.event.id});
    },
    handleSubmit: function(e) {
        this.props.handleSubmit(e, this.props.event, {
            title: this.state.hottitle
        });

        this.setState({
            hasChanged: false
        });
    },
    handleDelete: function(e) {
        this.props.handleDelete(e, this.props.event);
    },
    render: function() {

        var self = this;

        var start = this.props.event.start,
            end = this.props.event.end;

        var timespan = null;

        if(start.isSame(end.clone().subtract(1, 'millisecond'), 'day')) {
            timespan = StringUtils.capitalizeFirst(DateTimeUtils.dateRelativeToToday(start, this.props.today)) + ', ' + DateTimeUtils.allDayOrTimeRange(start, end);
        } else {
            if(start.isSame(end, 'month')) {
                timespan = start.format('MMMM Do, HH:mm') + ' to ' + end.format('MMM Do YYYY HH:mm');
            } else {
                if(start.isSame(end, 'year')) {
                    timespan = start.format('MMM Do, HH:mm') + ' to ' + end.format('MMM Do YYYY, HH:mm');
                } else {
                    timespan = start.format('MMM Do YYYY, HH:mm') + ' to ' + end.format('MMM Do YYYY, HH:mm');
                }
            }
        }

        var displayCalendarSelector = (CalendarStore.count() > 1),
            cals = displayCalendarSelector ? CalendarStore.getAll() : {},
            calendars = [];

        for(var calendarIndex in cals) {
            calendars.push(cals[calendarIndex]);
        }

        var content = (
            <div className="cal-week-event-detail-form">
                <p><textarea className="event-title" onChange={this.handleChange} value={this.state.hottitle} /></p>

                { displayCalendarSelector && (
                    <div className="form-inline form-group">

                        <ColorSelect
                            items={calendars}
                            selected={this.props.event.calendar}
                            itemvalue={function(cal) { return cal.id; }}
                            itemlabel={function(cal) {
                                return (<div style={{display: 'inline-block'}}><div style={{width: '14px', 'height': '14px', display: 'inline-block', backgroundColor: cal.darkColor, position: 'relative', 'top': '2px'}}></div> <span>{cal.displayname}</span></div>);
                            }}
                            onChange={function(calendar) {

                                CalendarActionCreators.enable(calendar);
                                
                                EventActionCreators.changeCalendar(
                                    self.props.event,
                                    calendar
                                );
                            }}
                        />

                    </div>
                )}

                <p className="event-timespan">{timespan}</p>
                
                <div>
                    <button className="btn btn-default btn-sm btn-edit" onClick={this.handleEdit}>Edit</button>
                    {this.state.hasChanged && (<button className="btn btn-primary btn-sm btn-save" onClick={this.handleSubmit}>Save</button>)}
                    {!this.state.hasChanged && (<button className="btn btn-danger btn-sm btn-delete" onClick={this.handleDelete}>Delete</button>)}
                </div>
            </div>
        );

        return (
            <ArrowPopup
                top={this.props.top}
                left={this.props.left}
                width={this.props.width}
                height={this.props.height}
                anchorposition={this.props.anchorposition}
                anchoroffsettop={this.props.anchoroffsettop}
                className={['cal-week-event-detail-popup']}
                childs={content} />
        );
    }
});

module.exports = DetailPopup;