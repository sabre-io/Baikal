'use strict';

var timeAlignment = 0.5;    // half an hour

var determineTimeByY = function(y, hourheight, unaligned) {
    
    y = parseInt(y);

    var mom = y / hourheight;
    var hour = parseInt(mom);
    var time = mom - hour;
    
    if(!unaligned) {
        if(time >= timeAlignment) {
            return hour + timeAlignment;
        } else {
            return hour;
        }
    }

    return hour + time;
};

var getNumericalHourFromMoment = function(when) {
    return when.hour() + (when.minute() / 60);
};

var determineYByTime = function(time, hourheight, unaligned) {
    
    if(!unaligned) {
        var hour = parseInt(time);
        var minute = time - hour;
        time = hour;
        if(minute >= timeAlignment) time += timeAlignment;
    }

    return time * hourheight;
};

var determineDateAndTimeBySurfaceXY = function(x, y, weekstartdate, hourheight, daywidth, allowMovingToAdjacentWeeks) {

    var daynum = parseInt((x / daywidth)); // monday = 0, sunday = 6
    
    if(allowMovingToAdjacentWeeks) {
        if(x < 0) {
            daynum = -1;
        } else if(daynum > 6) {
            daynum = 7;
        }
    } else {
        if(daynum < 0) {
            daynum = 0;
        } else if(daynum > 6) {
            daynum = 6;
        } 
    }

    var time = determineTimeByY(y, hourheight),  // time: numerical hour
        nbhours = parseInt(time),
        nbminutes = (time % 1) * 60;

    return weekstartdate.clone().add(daynum, 'day').add(nbhours, 'hour').add(nbminutes, 'minute');
};

module.exports = {
    determineTimeByY: determineTimeByY,

    determineTimeByX: function(x, daywidth) {
        var days = parseInt(x / daywidth);
        return days * 24;
    },

    determineYByMoment: function(when, hourheight, unaligned) {
        return determineYByTime(getNumericalHourFromMoment(when), hourheight, unaligned);
    },

    determineYByTime: determineYByTime,

    determineXByDate: function(when, daywidth, hourbarwidth) {
        return ((when.isoWeekday()-1) * daywidth) + hourbarwidth;
    },

    determineDateAndTimeBySurfaceXY: determineDateAndTimeBySurfaceXY,
    determineDateAndTimeByPageXY: function(pagex, pagey, calendarx, calendary, weekstartdate, hourheight, daywidth, allowMovingToAdjacentWeeks) {
        // TODO: handle starttime, endtime and number of days displayed; for now, a day is always 24h, and there are always 7 days displayed
        var relx = pagex - calendarx,
            rely = pagey - calendary;

        return determineDateAndTimeBySurfaceXY(relx, rely, weekstartdate, hourheight, daywidth, allowMovingToAdjacentWeeks);
    },

    getNumericalHourFromMoment: getNumericalHourFromMoment,

    overlaps: function(daystart, dayend, occstart, occend) {

        // --------------<J                        J>------------------------
        // -----<E                                               E>----------      E.s <= J.s && E.e >= J.s
        // -----<E                         E>--------------------------------      E.s <= J.s && E.e >= J.s
        // --------------------------<E   E>---------------------------------      E.s >= J.s && E.e <= J.e
        // --------------------<E                                E>----------      E.s <= J.e && E.e >= J.e
        
        return (
            (occstart >= daystart && occend <= dayend) ||
            (occstart <= daystart && occend > daystart) ||
            (occstart <= dayend && occend >= dayend)
        );
    },

    getAlignmentDeltaInMsForMoment: function(when) {
        var time = when.diff(when.clone().startOf('day'), 'hours', true);   // true: floating point value authorized
        var delta = Math.round((Math.abs(time) % timeAlignment) * 1000) / 1000;   // avoiding floating point approximations
        return delta * 3600 * 1000; // result in ms
    }
};