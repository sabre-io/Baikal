/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet;

var DaySelector = React.createClass({
    mixins: [PureRenderMixin],
    
    getInitialState: function() {

        // The calendar will initially display the month of the current displayed week
        return {
            firstDayOfMonth: this.props.startdate.clone().startOf('month')
        };
    },

    componentWillReceiveProps: function(nextProps) {

        // Aligning the displayed month with the one specified by props
        // Useful for:
        //     * following the selected day in the viewport when date is entered manually
        //     * dislaying the month of the selected day, when this day is one of the last days of the previous month (displayed in current month), or when this day is one of the first days of the newt month (also, displayed in current month)

        var newFirstDayOfMonth = nextProps.startdate.clone().startOf('month');
        if(!newFirstDayOfMonth.isSame(this.state.firstDayOfMonth, 'day')) {
            this.setState({firstDayOfMonth: newFirstDayOfMonth});
        }
    },

    render: function() {

        var self = this;
        
        var previousMonth = function(e) {
            self.setState({firstDayOfMonth: self.state.firstDayOfMonth.clone().subtract('1', 'day').startOf('month')});
        };

        var nextMonth = function(e) {
            self.setState({firstDayOfMonth: self.state.firstDayOfMonth.clone().endOf('month').add('1', 'day').startOf('month')});
        };

        var btnGoToSelectedDayClick = function() {
            viewDay(self.props.selectedDay);
        };

        var viewDay = function(date) {
            self.setState({firstDayOfMonth: date.clone().startOf('month')});
        }

        var dayClick = function(date) {
            self.props.selectDay(date.clone());
        };

        var firstMonday = this.state.firstDayOfMonth.clone().isoWeekday(1),
            lastSunday = this.state.firstDayOfMonth.clone().endOf('month').isoWeekday(7); // # aligning date on 00:00:00:000

        // Adding one extra weekrow if the last displayed sunday is also the last day of the displayed calendar
        if(lastSunday.isSame(this.state.firstDayOfMonth, 'month')) {
            lastSunday.add(7, 'days');
        }

        var weeks = [],
            weekstart = firstMonday.clone(),
            weekend = firstMonday.clone().add(7, 'days');

        while(weekstart.isBefore(lastSunday)) {
            weeks.push({start: weekstart.clone(), end: weekend.clone()});

            weekstart.add('7', 'days');
            weekend.add('7', 'days');
        }

        // Building DOM
        var weekRows = [],
            dayheaders = [];

        var dayHeaderDate = firstMonday.clone();

        for(var k = 1; k <= 7; k++) {
            dayheaders.push(<td key={k} className={cx({
                'cal-week-dayselector-dayheader': true
            })}>{dayHeaderDate.format('dd')}</td>);
            dayHeaderDate.add(1, 'day');
        }

        weekRows.push(<tr key="dayheaders" className="cal-week-dayselector-dayheaders">{dayheaders}</tr>);

        var lowerBound = null,
            upperBound = null,
            shavedUpperBound = null;

        var shavedSelectedDay = this.props.selectedDay ? this.props.selectedDay.clone() : null;

        for(var weekIndex in weeks) {
            var week = weeks[weekIndex];

            var weekDays = [],
                curdate = week.start.clone();

            for(k = 1; k <= 7; k++) {

                //var lowerBound = upperBound = null;

                if(this.props.rangeBound && this.props.selectedDay) {
                    
                    if(this.props.rangeBound.isBefore(this.props.selectedDay)) {
                        lowerBound = this.props.rangeBound.clone();
                        upperBound = this.props.selectedDay.clone();
                    } else {
                        lowerBound = this.props.selectedDay.clone();
                        upperBound = this.props.rangeBound.clone();
                    }

                    //upperBound.subtract(1, 'millisecond');
                }

                if(upperBound) {
                    if(upperBound.clone().startOf('day').isSame(upperBound)) {
                        var shavedUpperBound = upperBound.clone().subtract(1, 'millisecond');
                        if(this.props.selectedDay && this.props.selectedDay.isSame(upperBound, 'day')) {
                            shavedSelectedDay = this.props.selectedDay.clone().subtract(1, 'millisecond').startOf('day');
                        }
                    } else {
                        var shavedUpperBound = upperBound.clone();
                    }
                }

                var isToday = this.props.today && curdate.isSame(this.props.today, 'day'),
                    isCurDay = shavedSelectedDay && curdate.isSame(shavedSelectedDay, 'day'),
                    isCurMonth = curdate.isSame(this.state.firstDayOfMonth, 'month'),

                    isDisabledBefore = this.props.disableBefore && curdate.isBefore(this.props.disableBefore, 'day'),
                    isDisabledBeforeLastDay = isDisabledBefore && curdate.isSame(this.props.disableBefore, 'day'),

                    isDisabledAfter = this.props.disableAfter && curdate.isAfter(this.props.disableAfter, 'day'),
                    isDisabledAfterFirstDay = isDisabledAfter && curdate.isSame(this.props.disableAfter, 'day'),

                    isWithinRange = lowerBound && shavedUpperBound && (
                        curdate.isSame(lowerBound, 'day') ||
                        curdate.isSame(shavedUpperBound, 'day') ||
                        curdate.isBetween(lowerBound, shavedUpperBound, 'day')
                    ),
                    isWithinRangeFirstDay = isWithinRange && curdate.isSame(lowerBound, 'day'),
                    isWithinRangeLastDay = isWithinRange && curdate.isSame(shavedUpperBound, 'day');

                var onClick = null;

                if(!isDisabledBefore && !isDisabledAfter) {
                    onClick = dayClick.bind(this, curdate.clone());
                }

                weekDays.push(<td key={curdate.format()} onClick={onClick} className={cx({
                    'cal-week-dayselector-day': true,
                    'cal-week-dayselector-day-curmonth': isCurMonth,
                    'cal-week-dayselector-day-othermonth': !isCurMonth,
                    'cal-week-dayselector-day-curday': isCurDay,
                    'cal-week-dayselector-day-notcurday': !isCurDay,
                    'cal-week-dayselector-day-today': isToday,
                    'cal-week-dayselector-day-disabled': isDisabledBefore || isDisabledAfter,
                    'cal-week-dayselector-day-disabled-before-lastday': isDisabledBeforeLastDay,
                    'cal-week-dayselector-day-disabled-before': isDisabledBefore,
                    'cal-week-dayselector-day-disabled-after': isDisabledAfter,
                    'cal-week-dayselector-day-disabled-after-firstday': isDisabledAfterFirstDay,
                    'cal-week-dayselector-day-withinrange': isWithinRange,
                    'cal-week-dayselector-day-withinrange-firstday': isWithinRangeFirstDay,
                    'cal-week-dayselector-day-withinrange-lastday': isWithinRangeLastDay
                })}><div className="day-bg"></div><div className="day-range"></div><span className="day-number">{curdate.format('D')}</span></td>);

                curdate.add(1, 'day');
            }

            var isCurWeek = week.start.isSame(this.props.startdate, 'week');

            weekRows.push(
                <tr key={week.start.format()} className={cx({
                    'cal-week-dayselector-week': true,
                    'cal-week-dayselector-week-curweek': this.props.highlightSelectedDayWeek && isCurWeek
                })}>
                    {weekDays}
                </tr>
            );
        }

        var goToCurDayDisabled = false;
        if(
            this.props.selectedDay &&
            this.props.selectedDay.clone().startOf('month').isSame(this.state.firstDayOfMonth, 'day')
        ) {
            var goToCurDayDisabled = true;
        }

        var goToTodayDisabled = false;
        if(
            this.props.today &&
            this.props.today.clone().startOf('month').isSame(this.state.firstDayOfMonth, 'day')
        ) {
            var goToTodayDisabled = true;
        }

        var classNames = {
            'cal-week-dayselector': true
        };

        if(this.props.className) {
            for(var classNameIndex in this.props.className) {
                classNames[this.props.className[classNameIndex]] = true;
            }
        }

        // var debugFormat = 'YYYY-MM-DD HH:mm:ss:SSS';
        // {lowerBound && (<p>Lower: {lowerBound.format(debugFormat)}</p>)}
        // {upperBound && (<p>Upper: {upperBound.format(debugFormat)}</p>)}
        // {shavedUpperBound && (<p>ShavedUpper: {shavedUpperBound.format(debugFormat)}</p>)}
        // {this.props.selectedDay && (<p>selectedDay: {this.props.selectedDay.format(debugFormat)}</p>)}
        // {shavedSelectedDay && (<p>shavedSelectedDay: {shavedSelectedDay.format(debugFormat)}</p>)}

        return (
            <div className={cx(classNames)}>

                <div className="control-bar">
                    <div style={{width: '60%', 'float': 'left', textAlign: 'left'}} className="monthname">{this.state.firstDayOfMonth.format('MMM YYYY')}</div>
                    <div style={{width: '40%', 'float': 'left', textAlign: 'right'}}>
                        <div className="btn-group">
                            <button className="btn btn-xs btn-default" onClick={previousMonth}><i className="fa fa-caret-left"></i></button>
                            {this.props.selectedDay && (<button className={cx({
                                'btn': true,
                                'btn-xs': true,
                                'btn-default': true,
                                'disabled': goToCurDayDisabled
                            })} onClick={btnGoToSelectedDayClick}><i className="fa fa-dot-circle-o"></i></button>)}
                            {!this.props.selectedDay && this.props.today && (<button className={cx({
                                'btn': true,
                                'btn-xs': true,
                                'btn-default': true,
                                'disabled': goToTodayDisabled
                            })} onClick={viewDay.bind(this, this.props.today)}><i className="fa fa-dot-circle-o"></i></button>)}
                            <button className="btn btn-xs btn-default" onClick={nextMonth}><i className="fa fa-caret-right"></i></button>
                        </div>
                    </div>
                </div>
                <table>
                    <tbody>
                        {weekRows}
                    </tbody>
                </table>
            </div>
        );
    }
});

module.exports = DaySelector;