/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,

    CalendarStateStore = require('../stores/CalendarStateStore'),
    MomentSelector = require('../utils/MomentSelector'),

    LayerColumnheaders = require('./Surface/LayerColumnheaders.react'),
    LayerHourmarkers = require('./Surface/LayerHourmarkers.react'),
    LayerEvents = require('./Surface/LayerEvents.react');

var Surface = React.createClass({
    mixins: [PureRenderMixin],

    getInitialState: function() {
        return {
            windowresizelistenertoken: null,
            surfaceOffset: null,
            availableHeight: null,
            width: null
        };
    },
    componentDidMount: function() {
        this.handleWindowResize();
        this.setState({
            windowresizelistenertoken: this.props.resizeAddListener(this.handleWindowResize)
        });

        if(this.props.windowed) {

            var selectedEvent = CalendarStateStore.getSelectedEvent();

            var marginTop = 0.25 * this.props.hourheight;

            // Scrolling the container to the business start time at launch
            var scrollY = (this.props.businessstarttime * this.props.hourheight) - marginTop;

            if(selectedEvent) {
                // An event is selected; we rather scroll the container to that event
                scrollY = MomentSelector.determineYByMoment(selectedEvent.start, this.props.hourheight, true);    // true: unaligned
                scrollY -= marginTop;
            }

            if(scrollY < 0) scrollY = 0;

            // opacity + timeouts: hack to avoid surface perceptible flash scrolling
            $('.cal-week-surface').css({'opacity': 0});
            setTimeout(function() {
                $('.cal-week-surface').scrollTop(scrollY);
                setTimeout(function() {
                    $('.cal-week-surface').css({'opacity': 1});
                }, 0);
            }, 0);
        }
    },
    componentWillUnmount: function() {
        this.props.resizeRemoveListener(this.state.windowresizelistenertoken);
    },

    handleWindowResize: function(e) {

        var surfaceOffset = $('.cal-week-surface').offset(),
            viewportHeight = $(window).height(),
            availableHeight = viewportHeight - surfaceOffset.top;

        var newState = {
            surfaceOffset: surfaceOffset,
            availableHeight: availableHeight
        };

        var width = $(this.getDOMNode()).width();
        if(width !== this.state.width) {
            // calendar width may be constrained to a CSS grid, thus it's width may remain the same when the window resizes
            newState.width = width;
        }
        
        this.setState(newState);
    },

    render: function() {

        var columnwidth = (this.state.width - this.props.hourbarwidth) / (this.props.enddate.diff(this.props.startdate, 'day') + 1),
            surfaceheight = ((this.props.endtime - this.props.starttime) + 1) * this.props.hourheight;

        var surfaceStyle = null;
        if(this.props.windowed) {
            surfaceStyle = {
                height: this.state.availableHeight + 'px',
                overflowY: 'scroll'
            };
        }

        return (
            <div>
                <table style={{width: '100%'}}>
                    <LayerColumnheaders
                        hourbarwidth={this.props.hourbarwidth}
                        hourheight={this.props.hourheight}
                        columnwidth={columnwidth}
                        startdate={this.props.startdate}
                        enddate={this.props.enddate}
                        today={this.props.today} />
                </table>
                <div style={surfaceStyle} className="cal-week-surface">
                    <table>
                        <LayerHourmarkers
                            hourbarwidth={this.props.hourbarwidth}
                            hourheight={this.props.hourheight}
                            starttime={this.props.starttime}
                            endtime={this.props.endtime}
                            businessstarttime={this.props.businessstarttime}
                            businessendtime={this.props.businessendtime} />
                        <LayerEvents
                            calendars={this.props.calendars}
                            disabledCalendars={this.props.disabledCalendars}
                            apiendpoint={this.props.apiendpoint}
                            changeRange={this.props.changeRange}
                            hourbarwidth={this.props.hourbarwidth}
                            hourheight={this.props.hourheight}
                            columnwidth={columnwidth}
                            surfacewidth={this.state.width}
                            surfaceheight={surfaceheight}
                            surfaceoffset={this.state.surfaceOffset}
                            startdate={this.props.startdate}
                            enddate={this.props.enddate}
                            today={this.props.today}
                            starttime={this.props.starttime}
                            endtime={this.props.endtime}
                            allowMovingToAdjacentWeeks={this.props.allowMovingToAdjacentWeeks}
                            windowed={this.props.windowed}
                            mouseMoveAddListener={this.props.mouseMoveAddListener}
                            mouseMoveRemoveListener={this.props.mouseMoveRemoveListener} 
                            mouseUpAddListener={this.props.mouseUpAddListener}
                            mouseUpRemoveListener={this.props.mouseUpRemoveListener}
                            resizeAddListener={this.props.resizeAddListener}
                            resizeRemoveListener={this.props.resizeRemoveListener}
                            cosmetic={this.props.cosmetic} />
                    </table>
                </div>
            </div>
        );
    }
});

module.exports = Surface;