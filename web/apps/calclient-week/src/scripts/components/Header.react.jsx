/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin;

var Header = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {
        var month = this.props.startdate.format('MMMM');
        var year = this.props.startdate.format('YYYY');
        var weeknumber = this.props.startdate.week();

        var monthLabel = [(<span className="monthname">{month}</span>)];

        if(!this.props.startdate.isSame(this.props.enddate, 'month')) {

            var secondmonth = this.props.enddate.format('MMMM');

            if(!this.props.startdate.isSame(this.props.enddate, 'year')) {

                var secondyear = this.props.enddate.format('YYYY');

                monthLabel.push(<span className="year"> {year} </span>);

                monthLabel.push(<span> - </span>);
                monthLabel.push(<span className="monthname">{secondmonth}</span>);
                monthLabel.push(<span className="year"> {secondyear} </span>);
            } else {
                monthLabel.push(<span> - </span>);
                monthLabel.push(<span className="monthname">{secondmonth}</span>);
                monthLabel.push(<span className="year"> {year} </span>);
            }
        } else {
            monthLabel.push(<span className="year"> {year} </span>);
        }

        return (
            <p className="cal-week-title"> {monthLabel} <span className="weeknumber">W{weeknumber}</span></p>
        );
    }
});

module.exports = Header;