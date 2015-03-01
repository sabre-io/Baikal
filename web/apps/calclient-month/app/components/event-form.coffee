`import Ember from 'ember'`

component = Ember.Component.extend

    event: null

    fromToLabel: (->

        utcStart = @get('event.start').clone().utc()
        utcEnd = @get('event.end').clone().utc()

        startIsMidnight = utcStart.isSame(utcStart.clone().startOf('day'))
        endIsMidnight = utcEnd.isSame(utcEnd.toString(), utcEnd.clone().subtract('1', 'second').endOf('day').add('1', 'second').toString())

        handleTimedRange = (utcStart, utcEnd) ->
        
            if utcStart.clone().startOf("day").isSame(utcEnd, "day")
                return utcStart.format("MMM Do YYYY, HH:mm") + " to " + utcEnd.clone().format("HH:mm")
            else
                if utcStart.isSame(utcEnd, "month")
                    return utcStart.format("MMMM Do HH:mm") + " to " + utcEnd.clone().format("MMM Do HH:mm, YYYY")
                else
                    if utcStart.isSame(utcEnd, "year")
                        return utcStart.format("MMMM Do HH:mm") + " to " + utcEnd.clone().format("MMMM Do HH:mm, YYYY")
                    else
                        return utcStart.format("MMMM Do HH:mm, YYYY") + " to " + utcEnd.clone().format("MMMM Do HH:mm, YYYY")

        if startIsMidnight and endIsMidnight
                
                utcEnd.subtract "1", "second"

                if utcStart.clone().startOf("day").isSame(utcEnd, "day")
                    return utcStart.format("dddd, MMMM Do YYYY")
                else
                    if utcStart.isSame(utcEnd, "month")
                        return utcStart.format("MMMM Do") + " to " + utcEnd.clone().format("Do, YYYY")
                    else
                        if utcStart.isSame(utcEnd, "year")
                            return utcStart.format("MMMM Do") + " to " + utcEnd.clone().format("MMMM Do, YYYY")
                        else
                            return utcStart.format("MMMM Do, YYYY") + " to " + utcEnd.clone().format("MMMM Do, YYYY")

        return handleTimedRange(utcStart, utcEnd)

    ).property('event.start', 'event.end')

`export default component`