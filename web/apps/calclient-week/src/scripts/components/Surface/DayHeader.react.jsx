/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet;

var DayHeader = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {
        
        var classes = cx({
            'cal-week-day-header': true,
            'cal-week-is-today': this.props.isToday
        });

        var dayname = this.props.day.format('ddd');
        var daynum = this.props.day.format('D');

        return (
            <td className={classes} style={{width: this.props.columnwidth + 'px'}}><span className="dayname">{dayname}</span> <span className="daynum">{daynum}</span></td>
        );
    }
});

module.exports = DayHeader;