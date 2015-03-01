'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    DayHeader = require('./DayHeader.react');

var LayerColumnheaders = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {

        var days = [];

        for(var curdate = this.props.startdate.clone(); curdate.isBefore(this.props.enddate); curdate.add(1, 'day')) {

            var isToday = (curdate.isSame(this.props.today));

            days.push(<DayHeader key={curdate.format()} day={curdate.clone()} isToday={isToday} columnwidth={this.props.columnwidth} />);
        }

        return (
            <tr className="cal-week-day-headers">
                <td style={{width: this.props.hourbarwidth + 'px'}}></td>
                {days}
            </tr>
        );
    }
});

module.exports = LayerColumnheaders;