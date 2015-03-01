var isAllDay = function(startmoment, endmoment) {
    if(
        startmoment.clone().startOf('day').isSame(startmoment) &&
        endmoment.clone().startOf('day').isSame(endmoment)
    ) {
        return true;
    }

    return false;
};

var allDayStringOrCallback = function(startmoment, endmoment, alldaystring, cbk) {
    return isAllDay(startmoment, endmoment) ? alldaystring : cbk(startmoment, endmoment);
};

var DateTimeUtils = {
    
    isAllDay: isAllDay,

    allDayOrStartTime: function(startmoment, endmoment) {
        return allDayStringOrCallback(startmoment, endmoment, 'all day', function() {
            return startmoment.format('HH:mm');
        });
    },

    allDayOrTimeRange: function(startmoment, endmoment) {
        return allDayStringOrCallback(startmoment, endmoment, 'all day', function() {
            return startmoment.format('HH:mm') + ' to ' + endmoment.format('HH:mm');
        });
    },

    dateRelativeToToday: function(subjectmoment, todaymoment) {
        if(subjectmoment.isSame(todaymoment, 'day')) {
            return 'today';
        }

        if(subjectmoment.isSame(todaymoment.clone().add(1, 'day'), 'day')) {
            return 'tomorrow';
        }
     
        if(subjectmoment.isSame(todaymoment.clone().subtract(1, 'day'), 'day')) {
            return 'yesterday';
        }

        if(subjectmoment.isSame(todaymoment, 'year')) {
            return subjectmoment.format('MMMM Do');
        }

        return subjectmoment.format('MMMM Do YYYY');
    }
};

module.exports = DateTimeUtils;