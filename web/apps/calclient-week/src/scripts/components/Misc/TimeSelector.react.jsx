/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    moment = require('moment');

var TimeSelector = React.createClass({
    mixins: [PureRenderMixin],
    getInitialState: function() {

        // The calendar will initially display the month of the current displayed week
        return {
            time: this.props.datetime.format('HH:mm'),
            error: false
        };
    },
    momentFromTxtTime: function(time) {
        return moment('2015-01-10 ' + time, 'YYYY-MM-DD HH:mm');
    },
    isTimeValid: function(time) {
        return this.getTimeParts(time) !== false;
    },
    getTimeParts: function(time) {

        var re = /^([0-9]{1,2})([:\s;\.,]|)([0-9]{1,2})$/;
        var m;
        if((m = time.match(re))) {
            
            var hour = parseInt(m[1]),
                minute = parseInt(m[3]);

            if(
                hour >= 0 && hour < 24 &&
                minute >= 0 && minute < 60
            ) {
                return {
                    hour: hour,
                    minute: minute
                };
            }
        }
        
        return false;
    },
    formatTime: function(time) {
        var timeParts = this.getTimeParts(time);
        return (timeParts.hour > 9 ? timeParts.hour : '0' + timeParts.hour) + ':' + (timeParts.minute > 9 ? timeParts.minute : '0' + timeParts.minute);
    },
    onChange: function(e) {

        var newState = { time: e.target.value };

        if(this.isTimeValid(newState.time)) {
            var timeParts = this.getTimeParts(newState.time);
            this.props.timeChanged(
                timeParts.hour,
                timeParts.minute
            );
            newState.error = false;
        } else {
            newState.error = true;
        }

        this.setState(newState);
    },
    handleBlur: function(e) {

        if(this.isTimeValid(e.target.value)) {
            console.log('VALID !');
            var newTime = this.formatTime(e.target.value);
            this.setState({time: newTime});
        } else {
            console.log('NOT VALID !');
        }
    },
    render: function() {

        var style = {};

        if(this.state.error) {
            style.backgroundColor = 'red';
            style.color = 'white';
        }

        return (
            <input type="text" className="form-control input-sm" style={style} value={this.state.time} onChange={this.onChange} onBlur={this.handleBlur} />
        );
    }
});

module.exports = TimeSelector;