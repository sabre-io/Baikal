/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet,
    MomentSelector = require('../../utils/MomentSelector'),
    StringUtils = require('../../utils/StringUtils'),
    DateTimeUtils = require('../../utils/DateTimeUtils');

var Event = React.createClass({
    mixins: [PureRenderMixin],
    handleMouseDown: function(e) {
        e.stopPropagation();
        this.props.movingEventBegin(e, this.props.event);
    },
    handleMouseEnter: function(e) {
        e.stopPropagation();
        this.props.hoveringEventBegin(e, this.props.event);
    },
    handleMouseLeave: function(e) {
        e.stopPropagation();
        this.props.hoveringEventEnd(e);
    },
    handleGrabMouseDown: function(e) {
        e.stopPropagation();
        this.props.resizingEventBegin(e, this.props.event);
    },
    render: function() {

        var startedbefore = false,
            endedafter = false;

        if(this.props.event.start.isSame(this.props.day, 'day')) {
            var timestart = MomentSelector.getNumericalHourFromMoment(this.props.event.start);
        } else {
            var timestart = 0;
            startedbefore = true;
        }

        if(
            this.props.event.end.isSame(this.props.day, 'day') ||
            this.props.event.end.clone().subtract(1, 'second').isSame(this.props.day, 'day')
        ) {
            var timeend = MomentSelector.getNumericalHourFromMoment(this.props.event.end);
            if(timeend === 0) {
                // ends at 00:00 next day
                timeend = 24;
            }
        } else {
            var timeend = 24;
            endedafter = true;
        }

        var gutterwidth = this.props.cosmetic.gutterwidth,
            topmargin = this.props.cosmetic.event.marginTop,
            bottommargin = this.props.cosmetic.event.marginBottom,
            lanexoffset = 6,
            lanewidthsupplement = 3;

        var top = (timestart * this.props.hourheight) + topmargin,
            left = this.props.lane * lanexoffset,
            duration = Math.abs(timeend - timestart),
            height = (duration * this.props.hourheight) - bottommargin,    // -bottommargin: margin between one event and the next
            width = this.props.columnwidth - left - gutterwidth + (this.props.lane * lanewidthsupplement);       // gutter left free: allows for event creation

        var classes = cx({
            'cal-week-event': true,
            'cal-week-event-displacing': this.props.isDisplacing,
            'cal-week-event-resizing': this.props.isResized,
            'cal-week-event-selected': this.props.isSelected,
            'cal-week-event-startedbefore': startedbefore,
            'cal-week-event-endedafter': endedafter,
            'cal-week-event-temporary': this.props.event.temporary,
            'cal-week-event-inday': !startedbefore && !endedafter
        });

        var inner = [];

        if(!startedbefore) {
            inner = [
                (<span key={1} className="cal-week-event-time">{StringUtils.capitalizeFirst(DateTimeUtils.allDayOrStartTime(this.props.event.start, this.props.event.end))}</span>),
                (<span key={2} className="cal-week-event-title">{this.props.event.title}</span>)
            ];
        } else {
            inner = [(<span key={1} className="cal-week-event-title">{this.props.event.title}</span>)];
        }

        var style = {
            position: 'absolute',
            width: width + 'px',
            top: top + 'px',
            marginLeft: left + this.props.cosmetic.event.marginLeft + 'px',
            height: height + 'px'
        };

        if(!this.props.event.temporary) {
            var isActive = (this.props.isDisplacing || this.props.isResized || this.props.isSelected);

            style.backgroundColor = isActive ? this.props.event.calendar.darkColor : this.props.event.calendar.lightColor;
            style.borderLeftColor = this.props.event.calendar.color;
            style.color = isActive ? this.props.event.calendar.darkColorText : this.props.event.calendar.lightColorText;
        }
        
        return (
            <div className={classes} style={style} onMouseDown={this.handleMouseDown}>
                <div className="cal-week-event-inner">{inner}</div>
                {!endedafter && (<div className="cal-week-event-bottom" onMouseDown={this.handleGrabMouseDown}>&nbsp;</div>)}
            </div>
        );
    }
});

module.exports = Event;