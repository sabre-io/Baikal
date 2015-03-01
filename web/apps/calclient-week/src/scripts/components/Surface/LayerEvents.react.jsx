/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    
    TimeHeader = require('./TimeHeader.react'),
    DayColumn = require('./DayColumn.react'),
    DetailPopup = require('./DetailPopup.react'),
    NowLine = require('./NowLine.react'),
    MomentSelector = require('../../utils/MomentSelector'),

    AppStateStore = require('../../stores/AppStateStore'),
    CalendarStateStore = require('../../stores/CalendarStateStore'),
    EventStore = require('../../stores/EventStore'),

    EventActionCreators = require('../../actions/EventActionCreators'),
    CalendarActionCreators = require('../../actions/CalendarActionCreators'),
    CalendarStateActionCreators = require('../../actions/CalendarStateActionCreators');

var LayerEvents = React.createClass({
    mixins: [PureRenderMixin],

    getInitialState: function() {
        return {
            creatingdirection: 1,
            creatingpivot: null,
            creatingpivotprecise: null,
            creatingduration: 0,
            
            displacing: false,
            displacingdisplaced: false,
            displacingevent: null,
            displacingmovelistenertoken: null,
            displacinguplistenertoken: null,
            displacingclickOffsetInMs: null,
            displacingorigstart: null,
            
            //selectedevent: null,

            resizing: false,
            resizingresized: false,
            resizingevent: null,
        };
    },

    componentDidMount: function() {
        EventStore.setup({
            apiendpoint: this.props.apiendpoint
        });
        EventStore.addChangeListener(this.onEventStoreChange);

        CalendarStateStore.addChangeListener(this.onCalendarStateStoreChange);
    },

    componentWillUnmount: function() {
        EventStore.removeChangeListener(this.onEventStoreChange);

        CalendarStateStore.removeChangeListener(this.onCalendarStateStoreChange);
    },

    onCalendarStateStoreChange: function() {
        this.forceUpdate();
    },

    componentWillReceiveProps: function(nextProps) {

        if(
            CalendarStateStore.getSelectedEvent() &&
            CalendarStateStore.getSelectedEvent().calendar.disabled === true
        ) {

            // de-selecting current event if it belongs to a disabled calendar
            CalendarStateActionCreators.selectEvent(null);
        }
    },

    onEventStoreChange: function() {
        this.forceUpdate();
    },

    handleEventResizeBegin: function(e, event) {
        if(this.state.resizing) return;

        //console.log('Resize ' + event.title + ' !!!');

        this.setState({
            resizing: true,
            resizingresized: false,
            resizingevent: event,
            resizingorigend: event.end.clone(),
            resizingmovelistenertoken: this.props.mouseMoveAddListener(this.handleEventResizeContinue),
            resizinguplistenertoken: this.props.mouseUpAddListener(this.handleEventResizeEnd)
        });
    },

    handleEventResizeContinue: function(e) {
        e.stopPropagation();
        if(!this.state.resizing) return;

        var relX = e.pageX - this.props.surfaceoffset.left - this.props.hourbarwidth,
            relY = e.pageY - this.props.surfaceoffset.top + $('.cal-week-surface').scrollTop();

        var projecteddate = MomentSelector.determineDateAndTimeBySurfaceXY(
            relX, relY,
            this.props.startdate,
            this.props.hourheight,
            this.props.columnwidth,
            this.props.allowMovingToAdjacentWeeks
        );

        if(
            projecteddate.isBefore(this.state.resizingevent.start) ||
            projecteddate.isSame(this.state.resizingevent.start) ||
            Math.abs(projecteddate.diff(this.state.resizingevent.start, 'hour', true)) < 0.5
        ) {
            // resize is negative, or resulting event is too small (< 0.5h)
            if(
                (projecteddate.hour() * 60) + projecteddate.minute() >=
                (this.state.resizingevent.start.hour() * 60) + this.state.resizingevent.start.minute()
            ) {
                // the resize is negative, but the time (hour+minute) is above the current starting date of the event
                // the user is probably dragging the event a bit to far on the left, thus pointing at a time in the previous day
                // we correct this, and consider it a time in the current day
                projecteddate = this.state.resizingevent.start.clone().hour(projecteddate.hour()).minute(projecteddate.minute());
            } else {
                projecteddate = this.state.resizingevent.start.clone().add(30, 'minute');
            }
        } else {
            // Adding 30 minutes to highlight currently hovered cell
            projecteddate.add(30, 'minute');
        }

        var alignmentMs = MomentSelector.getAlignmentDeltaInMsForMoment(projecteddate);
        projecteddate.add(alignmentMs, 'millisecond');

        if(!projecteddate.isSame(this.state.resizingevent.end)) {

            //console.log('Resize continue ' + this.state.resizingevent.title + ' !!!');

            // event has been resized

            EventActionCreators.resizeEvent(
                this.state.resizingevent,
                projecteddate
            );

            if(!this.state.resizingresized) {
                this.setState({
                    resizingresized: true   // useful to determine if it is necessary to sync
                });
            }
        }
    },

    handleEventResizeEnd: function(e) {
        e.stopPropagation();
        if(!this.state.resizing) return;

        //console.log('Resize ends ' + this.state.resizingevent.title + ' !!!');

        this.props.mouseMoveRemoveListener(this.state.resizingmovelistenertoken);
        this.props.mouseUpRemoveListener(this.state.resizinguplistenertoken);

        if(!this.isMounted()) return;

        if(!this.state.resizingorigend.isSame(this.state.resizingevent.end)) {
            EventActionCreators.resizedEvent(this.state.resizingevent);
        }

        this.setState({
            resizing: false,
            resizingresized: false,
            resizingevent: null,
            resizingmovelistenertoken: null,
            resizinguplistenertoken: null,
            resizingorigend: null
        });
    },

    handleEventMoveBegin: function(e, event) {
        //console.log('Moving begins !');

        if(this.state.displacing) return;

        var relX = e.pageX - this.props.surfaceoffset.left - this.props.hourbarwidth,
            relY = e.pageY - this.props.surfaceoffset.top + $('.cal-week-surface').scrollTop();

        var clickOriginDate = MomentSelector.determineDateAndTimeBySurfaceXY(
            relX, relY,
            this.props.startdate,
            this.props.hourheight,
            this.props.columnwidth
        );

        // determine the time offset between the click and the start of the event
        var clickOffsetInMs = clickOriginDate.diff(event.start);

        this.setState({
            //selectedevent: null,  // handled in handleEventMoveEnd; allows for event toggling
            displacing: true,
            displacingevent: event,
            displacingmovelistenertoken: this.props.mouseMoveAddListener(this.handleEventMoveContinue),
            displacinguplistenertoken: this.props.mouseUpAddListener(this.handleEventMoveEnd),
            displacingclickOffsetInMs: clickOffsetInMs,
            displacingorigstart: event.start.clone()
        });
    },

    handleEventMoveContinue: function(e) {
        // onMouseMove for this event, but handled on window to catch every movement, even if not on the event anymore

        e.stopPropagation();
        if(!this.state.displacing) return;

        var /*offsetX = (e.offsetX === undefined) ? e.originalEvent.layerX : e.offsetX,*/
            offsetY = (e.offsetY === undefined) ? e.originalEvent.layerY : e.offsetY;

        var relX = e.pageX - this.props.surfaceoffset.left - this.props.hourbarwidth,
            relY = $(e.target).offset().top + offsetY + $('.cal-week-surface').scrollTop() - this.props.surfaceoffset.top;

        /*console.log({
            epageX: e.pageX,
            epageY: e.pageY,
            daywidth: this.props.columnwidth,
            relX: relX,
            relY: relY,
            eoffsetX: offsetX,
            eoffsetY: offsetX,
            surfaceoffsetLeft: this.props.surfaceoffset.left,
            surfaceoffsetTop: this.props.surfaceoffset.top,
            targetOffsetTop: $(e.target).offset().top,
            surfaceScrollTop: $('.cal-week-surface').scrollTop(),
        });*/
        
        var projecteddate = MomentSelector.determineDateAndTimeBySurfaceXY(
            relX, relY,
            this.props.startdate,
            this.props.hourheight,
            this.props.columnwidth,
            this.props.allowMovingToAdjacentWeeks
        );

        var offsetedDate = projecteddate.subtract(this.state.displacingclickOffsetInMs, 'millisecond');

        var alignmentMs = MomentSelector.getAlignmentDeltaInMsForMoment(offsetedDate);

        offsetedDate.add(alignmentMs, 'millisecond');

        if(!offsetedDate.isSame(this.state.displacingevent.start)) {

            // event has been moved around

            EventActionCreators.displaceEvent(
                this.state.displacingevent,
                offsetedDate
            );

            if(!this.state.displacingdisplaced) {
                this.setState({
                    displacingdisplaced: true   // useful to determine if it was a click or a displacement
                });
            }
        }
    },

    handleEventMoveEnd: function(e) {
        //console.log('Moving ends !');
        e.stopPropagation();

        if(!this.state.displacing) return;

        this.props.mouseMoveRemoveListener(this.state.displacingmovelistenertoken);
        this.props.mouseUpRemoveListener(this.state.displacinguplistenertoken);

        if(!this.isMounted()) return;

        var newState = {
            displacing: false,
            displacingdisplaced: false,
            displacingevent: null,
            displacingmovelistenertoken: null,
            displacinguplistenertoken: null,
            displacingclickOffsetInMs: null,
            displacingorigstart: null//,
            //clicktime: moment().format()
        };

        if(this.state.displacingdisplaced) {
            // event has been moved around
            if(!this.state.displacingorigstart.isSame(this.state.displacingevent.start)) {
                EventActionCreators.displacedEvent(this.state.displacingevent);

                if(!this.state.displacingevent.start.isSame(this.props.startdate, 'isoWeek')) {
                    // event has been displaced to another week
                    // we are updating the displayed range accordingly
                    // and we select the event (to make it easier to spot)

                    CalendarStateActionCreators.selectEvent(this.state.displacingevent);

                    this.props.changeRange({
                        start: this.state.displacingevent.start.clone().startOf('isoWeek'),
                        end: this.state.displacingevent.start.clone().endOf('isoWeek')
                    });
                }
            }
        } else {
            // event has been clicked, not moved around

            if(CalendarStateStore.getSelectedEvent() && CalendarStateStore.getSelectedEvent().id === this.state.displacingevent.id) {
                //console.log('DESELECT !');
                CalendarStateActionCreators.selectEvent(null);
            } else {
                //console.log('SELECT !');
                //newState.selectedevent = this.state.displacingevent;
                CalendarStateActionCreators.selectEvent(this.state.displacingevent);
            }
            
        }

        this.setState(newState);
    },

    determineBoundaryMoments: function(pivot, direction, duration) {
        var endmoment = null,
            startmoment = null;

        if(pivot) {
            if(direction === -1) {
                endmoment = pivot.clone();
                startmoment = endmoment.clone().subtract(duration, 'hours');
            } else {
                startmoment = pivot.clone();
                endmoment = startmoment.clone().add(duration, 'hours');
            }
        }

        return {
            start: startmoment,
            end: endmoment
        };
    },
    momentFromDateAndTime: function(date, time) {
        var hour = parseInt(time);
        var minute = (time % 1 >= 0.5) ? 30 : 0;

        return date.clone().startOf('day').hour(hour).minute(minute);
    },
    preciseMomentFromDateAndTime: function(date, time) {
        var hour = parseInt(time);
        var minute = parseInt((time - hour) * 60);

        return date.clone().startOf('day').hour(hour).minute(minute);
    },
    creatingEventBegin: function(date, y) {

        var time = MomentSelector.determineTimeByY(y, this.props.hourheight, true);
        var pivotdate = this.momentFromDateAndTime(date, time),
            precisepivotdate = this.preciseMomentFromDateAndTime(date, time);

        this.setState({
            creatingdirection: 1,
            creatingpivot: pivotdate,
            creatingpivotprecise: precisepivotdate,
            creatingduration: 0
        });
    },
    creatingEventContinue: function(boundarydate, y) {

        if(!this.state.creatingpivot) return;

        var boundarytime = MomentSelector.determineTimeByY(y, this.props.hourheight, true);

        var boundarymoment = this.momentFromDateAndTime(boundarydate, boundarytime),
            preciseboundarymoment = this.preciseMomentFromDateAndTime(boundarydate, boundarytime);

        var difference = boundarymoment.diff(this.state.creatingpivot),
            precisedifference = preciseboundarymoment.diff(this.state.creatingpivotprecise);

        if(difference < 0) {
            direction = -1;
        } else {
            direction = 1;
        }

        duration = direction * (difference / 3600 / 1000);
        preciseduration = direction * (precisedifference / 3600 / 1000);

        if(preciseduration < 0.05) {
            // 0.05h (3mn, corresponding to 3px when hourheight=60px) movement: threshold for creation detection
            duration = 0;
        } else {
            if(direction === 1) duration += 0.5;    // including the currently hovered timeframe
        }

        if(this.state.creatingdirection != direction || this.state.creatingduration != duration) {
            this.setState({
                creatingdirection: direction,
                creatingduration: duration
            });
        }
    },
    creatingEventEnd: function() {

        if(!this.state.creatingpivot) return;

        var newState = {
            creatingdirection: 1,
            creatingpivot: null,
            creatingpivotprecise: null,
            creatingduration: 0
        };

        if(this.state.creatingduration === 0) {
            //console.log('DESELECT !');
            //newState.selectedevent = null;
            CalendarStateActionCreators.selectEvent(null);
        } else {
            var boundaries = this.determineBoundaryMoments(
                this.state.creatingpivot,
                this.state.creatingdirection,
                this.state.creatingduration
            );

            CalendarActionCreators.enable(AppStateStore.getFocusedCalendar());

            EventActionCreators.createEvent(
                AppStateStore.getFocusedCalendar(), {
                title: 'New event',
                start: boundaries.start.clone(),
                end: boundaries.end.clone(),
                busy: true
            });
        }

        this.setState(newState);
    },
    handlePopupSubmit: function(e, event, changedprops) {
        EventActionCreators.updateSomePropsEvent(
            event,
            changedprops
        );
    },
    handlePopupDelete: function(e, event) {
        EventActionCreators.deleteEvent(event);
        CalendarStateActionCreators.selectEvent(null);
    },
    render: function() {

        var lanes = [],
            expansionStart = this.props.startdate,
            expansionEnd = this.props.enddate;

        for(var curdate = this.props.startdate.clone(); curdate.isBefore(this.props.enddate); curdate.add(1, 'day')) {

            var isToday = (curdate.isSame(this.props.today, 'day'));
            var isoweekday = curdate.isoWeekday();
            var isWeekend = (isoweekday === 6 || isoweekday === 7);

            var boundaries = this.determineBoundaryMoments(this.state.creatingpivot, this.state.creatingdirection, this.state.creatingduration);

            var dayevents = EventStore.getDay(curdate);

            //var strDay = curdate.format('YYYY-MM-DD');
            //console.log(strDay, _events[strDay]);

            lanes.push(<DayColumn
                key={curdate.format()}
                hourheight={this.props.hourheight}
                day={curdate.clone()}
                isToday={isToday}
                isWeekend={isWeekend}
                columnwidth={this.props.columnwidth}
                creatingStart={boundaries.start}
                creatingEnd={boundaries.end}
                creatingEventBegin={this.creatingEventBegin}
                creatingEventContinue={this.creatingEventContinue}
                creatingEventEnd={this.creatingEventEnd}
                dayevents={dayevents}
                
                movingEventBegin={this.handleEventMoveBegin}
                resizingEventBegin={this.handleEventResizeBegin}
                
                movedEvent={this.state.displacingevent}
                resizedEvent={this.state.resizingevent}
                selectedEvent={CalendarStateStore.getSelectedEvent()}
                cosmetic={this.props.cosmetic} />

                // only 'begin' is handled on event; 'continue' and 'end' are handled by LayerEvents (using global mouse events, listened in movingEventBegin callback)
            );
        }

        // Laying out time headers

        var times = [];
        for(var time = this.props.starttime; time <= this.props.endtime; time++) {
            times.push(<TimeHeader key={time} time={time} hourheight={this.props.hourheight} />);
        }

        // Displaying "now line" if required

        var nowline = null;
        if(this.props.startdate.isSame(this.props.today, 'isoWeek')) {
            nowline = (<NowLine
                today={this.props.today}
                hourheight={this.props.hourheight}
                surfacewidth={this.props.surfacewidth}
                hourbarwidth={this.props.hourbarwidth}
                columnwidth={this.props.columnwidth}
            />);
        }

        // Displaying edition popup if required

        var popup = null;

        if(
            CalendarStateStore.getSelectedEvent() &&
            !this.state.displacing &&
            CalendarStateStore.getSelectedEvent().start.isSame(this.props.startdate, 'isoWeek')
        ) {

            var popupWidth = this.props.cosmetic.popup.width,
                popupHeight = this.props.cosmetic.popup.height;

            var eventTop = MomentSelector.determineYByMoment(CalendarStateStore.getSelectedEvent().start, this.props.hourheight),
                eventLeft = MomentSelector.determineXByDate(CalendarStateStore.getSelectedEvent().start, this.props.columnwidth, this.props.hourbarwidth);

            var popupTop = eventTop + this.props.cosmetic.event.marginTop,
                popupLeft = eventLeft - popupWidth + this.props.cosmetic.event.marginLeft,
                anchorposition = 'right',
                anchoroffsettop = 0;

            // figuring out if there's enough space to display the popup on the left
            var weekWidth = this.props.surfacewidth,
                weekHeight = this.props.surfaceheight,
                availableSpaceLeft = weekWidth - (weekWidth - eventLeft),
                availableSpaceBottom = weekHeight - popupTop;

            if(availableSpaceLeft < popupWidth) {
                // switching to right
                popupLeft = eventLeft + this.props.columnwidth - this.props.cosmetic.gutterwidth + this.props.cosmetic.event.marginLeft;
                anchorposition = 'left';
            }

            if(availableSpaceBottom < popupHeight) {
                popupTop = eventTop + (availableSpaceBottom - popupHeight) - this.props.cosmetic.event.marginTop + this.props.cosmetic.event.marginBottom;
                anchoroffsettop = eventTop - popupTop;
            }

            var popup = (
                <DetailPopup
                    top={popupTop}
                    left={popupLeft}
                    width={popupWidth}
                    height={popupHeight}
                    anchorposition={anchorposition}
                    anchoroffsettop={anchoroffsettop}
                    today={this.props.today}
                    event={CalendarStateStore.getSelectedEvent()}
                    handleSubmit={this.handlePopupSubmit}
                    handleDelete={this.handlePopupDelete}
                />
            );
        }

        return (
            <tr>
                <td className="cal-week-hours" style={{width: this.props.hourbarwidth + 'px'}}>
                    {times}
                    {nowline}
                    {popup}
                </td>
                {lanes}
            </tr>
        );
    }
});

module.exports = LayerEvents;