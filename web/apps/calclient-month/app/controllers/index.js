/* jshint unused:false */
/* global $, moment */

// Importing Ember
import Ember from 'ember';

// Importing Calendar resources
import CalendarRangeStoreMixin from 'ember-cli-cal/mixins/range-store';
import CalendarEventStoreMixin from 'ember-cli-cal/mixins/event-store';
import CalendarTools from 'ember-cli-cal/utilities/calendartools';

// Your controller should extend CalendarRangeStoreMixin and CalendarEventStoreMixin
// CalendarEventStoreMixin offers convenient routines to store and merge calendar events efficiently
// CalendarRangeStoreMixin handles date-ranges, and determines if a range has already been fetched or not (useful for AJAX event sources)
export default Ember.Controller.extend(CalendarRangeStoreMixin, CalendarEventStoreMixin, {

    // this holds the reference to the calendar component
    calendar: null,

    // the selected event, if any
    selectedEvent: null,

    windowheight: null,

    className: function() {
        if(this.config.get('fullscreen')) {
            return 'calendar-fullscreen';
        }

        return 'calendar-regular';

    }.property(),

    nbrows: function() {
        return this.get('calendar.rows.length');
    }.property('calendar.rows.length'),

    fullscreen: function() {
        return this.config.get('fullscreen');
    }.property(),

    calendarsurfaceheight: function() {
        if(this.config.get('fullscreen')) {
            return this.get('windowheight') - 60 - 33;
        } else {
            return this.get('windowheight') - 210;
        }
    }.property('windowheight'),

    // class binding to handle the "Selected event tab" in the view
    displayClass: function() {
        if (this.get('selectedEvent')) { return 'col-sm-9'; }
        return 'col-sm-12';
    }.property('selectedEvent'),

    calendarStyleTag: function() {

        // adapting the weekrow heights to the available vertical space
        return '<style type="text/css">' +

            '.bk-calendar .bk-weekrow,' +
            '.bk-calendar .bk-daycell {' +
                'min-height: ' + (this.get('calendarsurfaceheight') / this.get('nbrows')) + 'px !important;' +
            '}' +

        '</style>';
    }.property('calendarsurfaceheight', 'nbrows'),

    _setup: function() {
        this.set('eventcolor', this.config.get('color'));
        this.set('textcolor', this.config.get('textcolor'));

        var self = this;

        this.set('resizeHandler', function() {
            self.set('windowheight', $(window).height());
        });

        $(window).bind('resize', this.get('resizeHandler'));

        (this.get('resizeHandler'))();

    }.on('init'),

    willDestroy: function() {
        $(window).unbind('resize', this.get('resizeHandler'));
    },

    actions: {

        prev: function(event) {
            this.get('calendar').send('prev');
        },

        next: function(event) {
            this.get('calendar').send('next');
        },

        fullscreen: function(event) {
            document.location.href=this.config.get('alternativeurl');
        },

        // Called when a calendar event is selected
        // It's the controller responsibility to do something with the selected calendar event
        // Here, we inform the event that it's active, so that depending views may update (color changes, notably)
        eventSelected: function(event) {
            this.set('selectedEvent', event);
            this.get('selectedEvent').set('active', true);
            return false;
        },

        // Called when a calendar event is unselected
        eventUnselected: function() {
            this.get('selectedEvent').set('active', false);
            this.set('selectedEvent', false);
            return false;
        },

        // Called when the "Close" button is clicked for the selected event
        // This action is initialized by our controller, not by the calendar component
        closeEvent: function() {
            return this.calendar.unselectEvent();
        },

        viewChanged: function(range, oldrange) {
            // We fetch wider than requested (preloading)
            range.start.subtract(1, 'month');
            range.end.add(1, 'month');

            if (this.isRangeFetched(range)) {
                // CalendarRangeStoreMixin says that this range has been fetched already
                // We do nothing
                return;
            } else {
                // CalendarRangeStoreMixin says that this range has NOT already been fetched
                // We aggregate the range to the already fetched range, to keep track of this
                this.aggregateRange(range);
            }

            var that = this;

            $.ajax({
                url: this.config.get('url_expandedevents'),
                data: {
                    start: range.start.toISOString(),
                    end: range.end.toISOString()
                },
                success: function(events) {
                    var eventsOccurences = [];

                    $(events.expandedevent).each(function(key) {
                        $(events.expandedevent[key].occurences).each(function(occurencekey) {
                            eventsOccurences.push(
                                CalendarTools.DisplayedEvent.create({
                                    start: moment(events.expandedevent[key].occurences[occurencekey].start),
                                    end: moment(events.expandedevent[key].occurences[occurencekey].end),
                                    label: events.expandedevent[key].title,
                                    payload: events.expandedevent[key]
                                })
                            );
                        });
                    });

                    that.mergeEvents(eventsOccurences);
                }
            });
        }
    }
});
