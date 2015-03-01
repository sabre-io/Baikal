'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet,
    Event = require('./Event.react'),
    MomentSelector = require('../../utils/MomentSelector');

var DayColumn = React.createClass({
    mixins: [PureRenderMixin],
    handleMouseDown: function(e) {
        var y = e.pageY - $(e.target).offset().top;
        this.props.creatingEventBegin(this.props.day, y);
    },
    handleMouseMove: function(e) {
        var y = e.pageY - $(e.currentTarget).offset().top;
        this.props.creatingEventContinue(this.props.day, y);
    },
    handleMouseUp: function(e) {
        var y = e.pageY - $(e.target).offset().top;
        this.props.creatingEventEnd(this.props.day, y);
    },
    render: function() {

        var classes = cx({
            'cal-week-day': true,
            'cal-week-is-today': this.props.isToday,
            'cal-week-is-weekend': this.props.isWeekend
        });

        var creatingstarttime = null,
            creatingendtime = null,
            firstDay = false,
            lastDay = false;

        if(this.props.creatingStart) {

            if(this.props.creatingStart.isBefore(this.props.day, 'day')) {
                creatingstarttime = 0;
            } else if(this.props.creatingStart.isSame(this.props.day, 'day')) {
                creatingstarttime = this.props.creatingStart.hour();
                if(this.props.creatingStart.minute() == 30) creatingstarttime += 0.5;
                firstDay = true;
            }

            if(this.props.creatingEnd.isAfter(this.props.day, 'day')) {
                creatingendtime = 24;
            } else if(this.props.creatingEnd.isSame(this.props.day, 'day')) {
                lastDay = true;
                creatingendtime = this.props.creatingEnd.hour();
                if(this.props.creatingEnd.minute() == 30) creatingendtime += 0.5;
            }
        }

        var creatingEvent = null;

        if(creatingstarttime !== null && creatingendtime !== null && creatingendtime > creatingstarttime) {
            
            // calculating y offset

            var starty = MomentSelector.determineYByTime(creatingstarttime, this.props.hourheight),
                endy = MomentSelector.determineYByTime(creatingendtime, this.props.hourheight);

            var inner = null;

            if(firstDay) {
                if(this.props.creatingStart.isSame(this.props.creatingEnd, 'day')) {
                    inner = (<div>{this.props.creatingStart.format('HH:mm')} - {this.props.creatingEnd.format('HH:mm')}</div>);
                } else {
                    inner = (<div>{this.props.creatingStart.format('MMM DD HH:mm')}<br/>{this.props.creatingEnd.format('MMM DD HH:mm')}</div>);
                }
            }

            creatingEvent = (<div className="creatingevent" style={{
                zIndex: 10,
                'position': 'absolute',
                'top': starty + 'px',
                'height': (endy-starty) + 'px',
                'width': (this.props.columnwidth - 1) + 'px' // -1: borderwidth
            }}>{inner && (<div className="dates">{inner}</div>)}</div>);
        }

        // Displaying existing events

        this.props.dayevents.sort(function(a, b) {

            if(a.start < b.start) {
                return -1;
            } else if(a.start > b.start) {
                return 1;
            }

            if(a.end.diff(a.start) >= b.end.diff(b.start)) {
                return -1;
            }

            return 1;
        });

        var result = [],
            remainingEvents = this.props.dayevents.slice(0);    // shallow copy

        var previousEndTimeOnLane = {},
            lane = 0;

        for(var eIndex in remainingEvents) {
            var event = remainingEvents[eIndex];
            
            if(previousEndTimeOnLane[lane]) {
                if(previousEndTimeOnLane[lane] > event.start) {
                    lane++;
                } else {
                    while(previousEndTimeOnLane[lane-1] <= event.start && lane > 0) lane--;
                }
            }

            if(lane==0) { previousEndTimeOnLane={}; }

            result.push({lane: lane, event: event});
            previousEndTimeOnLane[lane] = event.end;
        }

        var existingEvents = [];

        for(var eventindex in result) {
            var isDisplacing = (this.props.movedEvent && this.props.movedEvent.id === result[eventindex].event.id),
                isResized = (this.props.resizedEvent && this.props.resizedEvent.id === result[eventindex].event.id),
                isSelected = (this.props.selectedEvent && this.props.selectedEvent.id === result[eventindex].event.id);

            existingEvents.push(<Event
                key={result[eventindex].event.calendar.id + '-' + result[eventindex].event.id}
                columnwidth={this.props.columnwidth}
                hourheight={this.props.hourheight}
                day={this.props.day}
                event={result[eventindex].event}

                movingEventBegin={this.props.movingEventBegin}
                resizingEventBegin={this.props.resizingEventBegin}

                isDisplacing={isDisplacing}
                isResized={isResized}
                isSelected={isSelected}

                lane={result[eventindex].lane}
                cosmetic={this.props.cosmetic} />

                // only 'begin' is handled on event; 'continue' and 'end' are handled by LayerEvents
            );
        }


        return (
            <td className={classes} onMouseDown={this.handleMouseDown} onMouseUp={this.handleMouseUp} onMouseMove={this.handleMouseMove} style={{width: this.props.columnwidth + 'px'}}>
                {creatingEvent}
                {existingEvents}
            </td>
        );
    }
});

module.exports = DayColumn;