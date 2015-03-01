/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin;

var TimeHeader = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {
        var time = this.props.time < 10 ? '0' + this.props.time : this.props.time;
        return (
            <div className="cal-week-time-header" style={{height: this.props.hourheight + 'px'}}><span className="cal-week-time-header-time">{time}:00</span></div>
        );
    }
});

module.exports = TimeHeader;