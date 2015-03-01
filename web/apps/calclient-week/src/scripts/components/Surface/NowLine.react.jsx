'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    moment = require('moment'),
    MomentSelector = require('../../utils/MomentSelector');

var NowLine = React.createClass({
    mixins: [PureRenderMixin],
    getInitialState: function() {
        return {
            now: moment(),
            ticker: null
        };
    },
    componentDidMount: function() {
        var self = this;
        var ticker = window.setInterval(function() {
            self.setState({now: moment()});
        }, 15000);

        this.setState({ticker: ticker});
    },
    componentWillUnmount: function() {
        if(this.state.ticker) window.clearInterval(this.state.ticker);
    },
    render: function() {

        var top = MomentSelector.determineYByMoment(
            this.state.now,
            this.props.hourheight,
            true    // unaligned
        );

        var left = this.props.hourbarwidth,
            width = this.props.surfacewidth - this.props.hourbarwidth;

        var nowpointLeft = MomentSelector.determineXByDate(
            this.state.now,
            this.props.columnwidth,
            this.props.hourbarwidth
        ) - this.props.hourbarwidth;

        var nowpointWidth = this.props.columnwidth,
            nowpointXOffset = 3;    // Y Offset of nowpoint, before and after day column

        var isoWeekDay = this.state.now.get('isoWeekDay');

        // adjusting offsets for Monday and Sunday
        if(isoWeekDay === 1) {
            nowpointLeft += nowpointXOffset;
            nowpointWidth -= nowpointXOffset;
        } else if(isoWeekDay === 7) {
            nowpointLeft += nowpointXOffset + 1; // +1: borderWidth
            nowpointXOffset = 0;
        }

        return (
            <div className="cal-week-nowline" style={{
                top: top + 'px',
                left: left + 'px',
                width: width + 'px'
            }}>
                <div className="cal-week-nowtime-wrapper">
                    <span className="cal-week-nowtime">{this.state.now.format('HH:mm')}</span>
                </div>
                <span className="cal-week-nowpoint" style={{
                    left: nowpointLeft + 'px',
                    width: nowpointWidth + (nowpointXOffset * 2)  - 1 + 'px'    /* -1: borderwidth */
                }}></span>
            </div>
        );
    }
});

module.exports = NowLine;