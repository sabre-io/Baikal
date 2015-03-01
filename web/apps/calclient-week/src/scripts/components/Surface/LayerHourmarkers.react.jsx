/** @jsx React.DOM */

'use strict';

var React = require('react'),
    PureRenderMixin = React.addons.PureRenderMixin,
    MarkerCell = require('./MarkerCell.react');

var LayerHourmarkers = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {

        var markers = [];

        for(var time = this.props.starttime; time <= this.props.endtime; time++) {
            var businesstime = (time >= this.props.businessstarttime && time <= this.props.businessendtime);
            var lastbusinesstime = (time === this.props.businessendtime);

            markers.push(<MarkerCell key={time} time={time} hourheight={this.props.hourheight} isBusinessTime={businesstime} isLastBusinessTime={lastbusinesstime} />);
        }

        return (
            <tr className="cal-week-hourmarkers-tr">
                <td></td>
                <td colSpan="7">
                    <div className="cal-week-hourmarkers-wrapper">
                        <div className="cal-week-hourmarkers">
                            {markers}
                        </div>
                    </div>
                </td>
            </tr>
        );
    }
});

module.exports = LayerHourmarkers;